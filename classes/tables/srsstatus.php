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

use mod_assign_external;
use moodle_url;
use table_sql;

require_once("$CFG->libdir/tablelib.php");
require_once("$CFG->dirroot/mod/assign/externallib.php");
class srsstatus extends table_sql {

    private $grades = null;
    private $data;
    private $gradeitem;

    public function __construct($data) {
        $this->useridfield = 'student';
        $this->data = $data;
        parent::__construct('report_grade-srs_status');
        $gradeitems = \local_quercus_tasks\api::get_quercus_gradeitems(
            $data->courseid, $data->assignment->id
        );
        // print_r($gradeitems);
        // There should only be one. Not less, not more. If there's not, there's a problem.
        $this->gradeitem = array_shift($gradeitems);
        // print_r($this->gradeitem);
        $this->scale = \local_quercus_tasks\api::get_scale($this->gradeitem->scaleid);
        // print_r($this->scale);
        $columns = [
            'id',
            'fullname',
            'grader',
            'solentgrade',
            'converted_grade',
            'processed',
            'payload_error',
            'timecreated',
            'timemodified'
        ];

        $columnheadings = [
            'ID',
            'Student',
            'Grader',
            'Solent grade',
            'Converted grade',
            'Status',
            'Error report',
            'Time queued',
            'Time processed'
        ];
        $this->define_columns($columns);
        $this->define_headers($columnheadings);
        $this->sortable(true, 'student');
        $this->collapsible(false);

        $this->define_baseurl(new moodle_url('/report/grade/srsstatus.php',
            ['id' => $data->courseid,
            'aid' => $data->assignment->id]));
        $select = "g.id, g.student, g.grader, g.assign, g.sitting,
            g.course, g.course_module, ag.grade solentgrade, g.converted_grade, g.response, g.parent_request_id,
            g.request_id, g.payload_error, g.processed, g.timecreated, g.timemodified,
            u.firstname, u.lastname, u.alternatename, u.lastnamephonetic, u.firstnamephonetic, u.middlename
            ";
        $from = "{local_quercus_grades} g
            JOIN {user} u ON u.id = g.student
            LEFT JOIN {assign_grades} ag ON ag.assignment = g.assign AND ag.userid = g.student
        ";
        $this->set_sql($select, $from, 'course = :courseid AND assign = :assign',
        ['courseid' => $data->courseid, 'assign' => $data->assignment->id]);
    }

    protected function col_grader($row) {
        $grader = \core_user::get_user($row->grader);
        return fullname($grader);
    }

    protected function col_timecreated($row) {
        return userdate($row->timecreated);
    }

    protected function col_timemodifed($row) {
        if ($row->timemodifed > 0) {
            return userdate($row->timemodified);
        }
        return '';
    }

    protected function col_solentgrade($row) {
        if (is_null($row->solentgrade)) {
            return 'Unmarked';
        }
        // Grades are saved as decimals.
        $gradeint = (int)$row->solentgrade;
        if ($gradeint == -1) {
            return 'Unmarked';
        }
        if (isset($this->scale->items[$gradeint])) {
            return $this->scale->items[$gradeint];
        }
        return get_string('scaleitemnotfound', 'report_grade');
    }

    /**
     * This function is not part of the public api.
     */
    function print_nothing_to_display() {
        global $OUTPUT;

        // Render the dynamic table header.
        echo $this->get_dynamic_table_html_start();

        // Render button to allow user to reset table preferences.
        echo $this->render_reset_button();

        $this->print_initials_bar();

        echo $OUTPUT->heading(get_string('nothingtodisplay'));
        if ($this->gradeitem)
        echo "<p>Reasons</p>";

        // Render the dynamic table footer.
        echo $this->get_dynamic_table_html_end();
    }
}

