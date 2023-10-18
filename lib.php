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
 * This file contains public API of grade report
 *
 * @package    report_grade
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This function extends the course navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_grade_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/grade:view', $context)) {
        $url = new moodle_url('/report/grade/index.php', ['id' => $course->id]);
        $navigation->add(get_string('pluginname', 'report_grade'),
            $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
        $url = new moodle_url('/report/grade/srsstatus.php', ['id' => $course->id]);
        $navigation->add(get_string('srsurl', 'report_grade'), $url, navigation_node::TYPE_SETTING);
    }
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function report_grade_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $array = [
        '*' => get_string('page-x', 'pagetype'),
        'report-*' => get_string('page-report-x', 'pagetype'),
        'report-grade-*' => get_string('page-report-grade-x',  'report_grade'),
    ];
    return $array;
}
