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

use report_grade\output\assignmentinfo;
use report_grade\tables\srsstatus as srsstatus_table;

require_once('../../config.php');

$aid = optional_param('aid', 0, PARAM_INT);
$courseid = required_param('id', PARAM_INT);
$coursecontext = context_course::instance($courseid);

require_login($courseid, false);
require_capability('report/grade:view', $coursecontext);
$course = $DB->get_record('course', ['id' => $courseid]);

$PAGE->set_url('/report/grade/srsstatus.php', array('id' => $courseid, 'aid' => $aid));
// $PAGE->set_pagelayout('report');
$PAGE->set_context($coursecontext);
$title = new lang_string('srstitlecourse', 'report_grade', ['shortname' => $course->shortname]);

$assignment = null;
if ($aid > 0) {
    $assignment = \local_quercus_tasks\api::get_quercus_assignment($aid);
    $title = new lang_string('srstitlecourseassignment', 'report_grade', [
        'shortname' => $course->shortname,
        'assignname' => $assignment->name]);
}
$PAGE->set_title($title);
$PAGE->set_heading($title);

// $PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('pluginname',  'report_grade'), new moodle_url('/report/grade/', ['id' => $courseid]));
$PAGE->navbar->add(get_string('srsstatus',  'report_grade'));

echo $OUTPUT->header();

// List SRS assignments.
$data = new stdClass();
$data->assignment = $assignment;
$data->courseid = $courseid;

$srsassignments = \local_quercus_tasks\api::get_quercus_assignments($data->courseid);

echo html_writer::tag('h3', $PAGE->heading);

if (!$srsassignments) {
    echo $OUTPUT->notification(new lang_string('noquercusassignments', 'report_grade'));
    echo $OUTPUT->footer();
    exit();
}
if (count($srsassignments) > 1) {
    // We only need to list assignments if there's more than one.
    $linklist = [];
    foreach ($srsassignments as $assign) {
        $linklist[] = html_writer::link(
            new moodle_url('/report/grade/srsstatus.php', [
                'id' => $data->courseid,
                'aid' => $assign->id
            ]), $assign->name);
    }
    echo html_writer::alist($linklist);
} else {
    // Get the first assignment from the array to output its table.
    $data->assignment = array_shift($srsassignments);
}

if ($data->assignment) {
    $assignmentinfo = new assignmentinfo($data->assignment);
    echo $OUTPUT->render($assignmentinfo);
    $table = new srsstatus_table($data);
    $table->out(100, true);
}

echo $OUTPUT->footer();
