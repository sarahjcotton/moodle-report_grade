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
 * Helper class for misc functions
 *
 * @package   report_grade
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2023 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_grade;

/**
 * Helper class for misc functions
 */
class helper {
    /**
     * Returns all the grade items for a course, or a single assignment.
     *
     * @param integer $courseid
     * @param integer $assignid
     * @return array
     */
    public static function get_gradeitems($courseid, $assignid = 0) {
        global $DB;
        $params = [
            'courseid' => $courseid,
        ];
        if ($assignid > 0) {
            $assignsql = ' AND gi.iteminstance = :assignid ';
            $params['assignid'] = $assignid;
        }

        $sql = "SELECT gi.*, cm.id cmid
        FROM {grade_items} gi
        JOIN {course_modules} cm ON cm.instance = gi.iteminstance
        JOIN {modules} m ON m.id = cm.module AND m.name = 'assign'
        WHERE gi.courseid = :courseid $assignsql AND gi.itemmodule = 'assign' AND cm.idnumber != ''";
        return $DB->get_records_sql($sql, $params);
    }

    /**
     * Gets the scale record, and explodes the scale item for easier handling.
     *
     * @param int $scaleid
     * @return stdClass|null
     */
    public static function get_scale($scaleid) {
        global $DB;
        $scale = $DB->get_record('scale', ['id' => $scaleid]);
        if (!$scale) {
            return null;
        }
        $scale->items = explode(',', $scale->scale);
        return $scale;
    }
}
