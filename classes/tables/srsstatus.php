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
 * SRS Status table
 *
 * @package   report_grade
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_grade\tables;

use lang_string;
use mod_assign_external;
use moodle_url;
use report_grade\helper;
use table_sql;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/tablelib.php");
require_once("$CFG->dirroot/mod/assign/externallib.php");
/**
 * Outputs the Student records marks upload status for given assignment
 */
class srsstatus extends table_sql {

    /**
     * Data required for assembling the table
     *
     * @var stdClass
     */
    private $data;
    /**
     * The grade item for this assignment
     *
     * @var stdClass
     */
    private $gradeitem;

    /**
     * Scale record with added exploded items
     *
     * @var stdClass
     */
    private $scale;

    /**
     * Constructor for table
     *
     * @param stdClass $data Containing courseid and assignment instance
     */
    public function __construct($data) {
        $this->useridfield = 'studentid';
        $this->data = $data;
        $this->set_attribute('id', 'report_grade-srs_status');
        parent::__construct('report_grade-srs_status');
        $columns = [
            'id',
            'fullname',
            'graderid',
            'solentgrade',
            'converted_grade',
            'status',
            'error_report',
            'timecreated',
            'timemodified'
        ];

        $columnheadings = [
            'id',
            new lang_string('student', 'report_grade'),
            new lang_string('grader', 'report_grade'),
            new lang_string('solentgrade', 'report_grade'),
            new lang_string('convertedgrade', 'report_grade'),
            new lang_string('status', 'report_grade'),
            new lang_string('errorreport', 'report_grade'),
            new lang_string('timequeued', 'report_grade'),
            new lang_string('timeprocessed', 'report_grade')
        ];
        $this->define_columns($columns);
        $this->define_headers($columnheadings);
        $this->sortable(true, 'student');
        $this->collapsible(false);
        $courseid = $data->courseid;
        $assignid = 0;
        $params = ['cid' => $courseid];
        if ($data->source == 'quercus') {
            $assignid = $data->assignment->id;
            $params['qid'] = $assignid;
        } else {
            $cmid = $data->assignment->get('cmid');
            $cm = get_fast_modinfo($courseid)->get_cm($cmid);
            $assignid = $cm->instance;
            $params['sid'] = $data->assignment->get('id');
        }
        $this->define_baseurl(new moodle_url('/report/grade/srsstatus.php', $params));
        $gradeitems = helper::get_gradeitems($courseid, $assignid);
        // There should only be one. Not less, not more. If there's not, there's a problem.
        $this->gradeitem = array_shift($gradeitems);
        $this->scale = helper::get_scale($this->gradeitem->scaleid);

        if ($data->source == 'quercus') {
            $select = "g.id, g.student studentid, g.grader graderid,
                ag.grade solentgrade, g.converted_grade, g.processed status,
                g.payload_error error_report, g.timecreated, g.timemodified,
                u.firstname, u.lastname, u.alternatename, u.lastnamephonetic, u.firstnamephonetic, u.middlename
                ";
            $from = "{local_quercus_grades} g
                JOIN {user} u ON u.id = g.student
                LEFT JOIN {assign_grades} ag ON ag.assignment = g.assign AND ag.userid = g.student
            ";
            $this->set_sql($select, $from, 'g.course = :courseid AND g.assign = :assignid',
                ['courseid' => $courseid, 'assignid' => $assignid]);
        } else {
            // This is SITS.
            $select = "g.id, g.studentid, g.graderid,
                ag.grade solentgrade, g.converted_grade, g.response status,
                g.message error_report, g.timecreated, g.timemodified,
                u.firstname, u.lastname, u.alternatename, u.lastnamephonetic, u.firstnamephonetic, u.middlename
                ";
            $from = "{local_solsits_assign} a
                JOIN {local_solsits_assign_grades} g ON g.solassignmentid = a.id
                JOIN {course_modules} cm ON cm.id = a.cmid
                JOIN {user} u ON u.id = g.studentid
                LEFT JOIN {assign_grades} ag ON ag.assignment = cm.instance AND ag.userid = g.studentid
            ";
            $this->set_sql($select, $from, 'a.cmid = :cmid',
                ['cmid' => $cmid]);
        }
    }

    /**
     * Grader column
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_graderid($row) {
        $grader = \core_user::get_user($row->graderid);
        return fullname($grader);
    }

    /**
     * Time created column
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_timecreated($row) {
        return userdate($row->timecreated);
    }

    /**
     * Time modified column
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_timemodified($row) {
        if ($row->timemodified > 0) {
            return userdate($row->timemodified);
        }
        return '';
    }

    /**
     * Solent converted grade
     *
     * @param stdClass $row
     * @return string
     */
    protected function col_solentgrade($row) {
        if (is_null($row->solentgrade)) {
            return 'Unmarked';
        }
        // Grades are saved as decimals.
        $gradeint = (int)$row->solentgrade;
        if ($gradeint == -1) {
            return 'Unmarked';
        }
        if (isset($this->scale->items[$gradeint - 1])) {
            return $this->scale->items[$gradeint - 1];
        }
        $scale = print_r($this->scale->items, true);
        return get_string('scaleitemnotfound', 'report_grade') . ' ' . $gradeint . $scale;
    }

    /**
     * This function is not part of the public api.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        // Render the dynamic table header.
        echo $this->get_dynamic_table_html_start();

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->print_initials_bar();

        echo $OUTPUT->heading(get_string('nogradestodisplay', 'report_grade'));

        // Render the dynamic table footer.
        echo $this->get_dynamic_table_html_end();
    }
}
