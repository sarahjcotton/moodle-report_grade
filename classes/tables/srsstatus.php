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

use table_sql;

require_once("$CFG->libdir/tablelib.php");
class srsstatus extends table_sql {
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);
        $this->useridfield = 'student';

        $columns = [
            'id',
            'student',
            'grader',
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
            'Converted grade',
            'Status',
            'Error report',
            'Time created',
            'Time modified'
        ];
        $this->define_columns($columns);
        $this->define_headers($columnheadings);
        $this->sortable(true, 'student');
        $this->collapsible(false);
    }
}

