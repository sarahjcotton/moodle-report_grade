<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * SRS Status page. Lists all grades that should be sent to quercus and their status.
 *
 * @package   report_grade
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_solsits\sitsassign;
use report_grade\output\assignmentinfo;
use report_grade\tables\srsstatus as srsstatus_table;

require_once('../../config.php');

$qid = optional_param('qid', 0, PARAM_INT); // Quercus.
$sid = optional_param('sid', 0, PARAM_INT); // SITS.
$courseid = required_param('cid', PARAM_INT);
$coursecontext = context_course::instance($courseid);

require_login($courseid, false);
require_capability('report/grade:view', $coursecontext);
$course = $DB->get_record('course', ['id' => $courseid]);

$title = new lang_string('srstitlecourse', 'report_grade', ['shortname' => $course->shortname]);

$data = new stdClass();
$data->courseid = $courseid;

// Get all Quercus and SITS assignments for this course.
$qassignments = \local_quercus_tasks\api::get_quercus_assignments($data->courseid);
// Using get_records_select to get id indexed array result. See MDL-79574.
$sassignments = sitsassign::get_records_select('courseid = :courseid AND cmid > 0', ['courseid' => $data->courseid]);
$totalassignments = count($qassignments) + count($sassignments);

$pageparams = [
    'cid' => $courseid,
];

if ($qid > 0) {
    $pageparams['qid'] = $qid;
    if (!isset($qassignments[$qid])) {
        // This assignment doesn't exist in this course. Throw an error.
        throw new moodle_exception('Invalid assignment specified');
    }
    $data->assignment = $qassignments[$qid];
    $data->source = 'quercus';
    $title = new lang_string('srstitlecourseassignment', 'report_grade', [
        'shortname' => $course->shortname,
        'assignname' => $data->assignment->name,
    ]);
}
if ($sid > 0) {
    $pageparams['sid'] = $sid;
    if (!isset($sassignments[$sid])) {
        // This assignment doesn't exist in this course. Throw an error.
        throw new moodle_exception('Invalid assignment specified');
    }
    $data->assignment = $sassignments[$sid];
    $data->source = ['sits'];
    $title = new lang_string('srstitlecourseassignment', 'report_grade', [
        'shortname' => $course->shortname,
        'assignname' => $data->assignment->get('title'),
    ]);
}

// If no assignment has been specified, find the first available one to display.
if ($totalassignments > 0 && !isset($data->assignment)) {
    if (count($qassignments) > 0) {
        $data->assignment = reset($qassignments);
        $data->source = 'quercus';
        $pageparams['qid'] = $data->assignment->id;
        $title = new lang_string('srstitlecourseassignment', 'report_grade', [
            'shortname' => $course->shortname,
            'assignname' => $data->assignment->name,
        ]);
    } else {
        $data->assignment = reset($sassignments);
        $data->source = 'sits';
        $pageparams['sid'] = $data->assignment->get('id');
        $title = new lang_string('srstitlecourseassignment', 'report_grade', [
            'shortname' => $course->shortname,
            'assignname' => $data->assignment->get('title'),
        ]);
    }
}

$PAGE->set_url('/report/grade/srsstatus.php', $pageparams);
$PAGE->set_context($coursecontext);

$PAGE->set_title($title);
$PAGE->set_heading($title);

$PAGE->navbar->add(get_string('pluginname',  'report_grade'), new moodle_url('/report/grade/index.php', ['id' => $courseid]));
$PAGE->navbar->add(get_string('srsstatus',  'report_grade'), new moodle_url('/report/grade/srsstatus.php', $pageparams));

echo $OUTPUT->header();

if ($totalassignments == 0) {
    echo $OUTPUT->notification(new lang_string('nosummativeassignments', 'report_grade'));
    echo $OUTPUT->footer();
    exit();
}

// We only need to list assignments if there's more than one.
if ($totalassignments > 1) {
    $linklist = [];
    foreach ($qassignments as $assign) {
        $linklist[] = html_writer::link(
            new moodle_url('/report/grade/srsstatus.php', [
                'cid' => $data->courseid,
                'qid' => $assign->id,
            ]), $assign->name);
    }
    foreach ($sassignments as $assign) {
        $linklist[] = html_writer::link(
            new moodle_url('/report/grade/srsstatus.php', [
                'cid' => $data->courseid,
                'sid' => $assign->get('id'),
            ]), $assign->get('title'));
    }
    echo html_writer::alist($linklist, ['class' => 'marksupload-assignment-list']);
}

if ($data->assignment) {
    $assignmentinfo = new assignmentinfo($data);
    echo $OUTPUT->render($assignmentinfo);
    $table = new srsstatus_table($data);
    $table->out(100, true);
}

echo $OUTPUT->footer();
