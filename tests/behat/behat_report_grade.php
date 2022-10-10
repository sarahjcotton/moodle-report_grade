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
 * Behat steps for Grade report
 *
 * @package   report_grade
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Behat Grade report steps
 */
class behat_report_grade extends behat_base {

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "report_grade > [page type]" page'.
     *
     * Recognised page names are:
     * | pagetype             | name meaning                             | description                                  |
     * | Grade report         | Course name                              | The grade report page for the course         |
     * | SRS status           | Course name                              | The SRS status report page for the course    |
     *
     * @param string $type identifies which type of page this is, e.g. 'View all submissions'.
     * @param string $identifier identifies the particular page, e.g. 'Assignment name'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        switch (strtolower($type)) {
            case 'grade report':
                $course = $this->get_course_id($identifier);
                return new moodle_url('/report/grade/index.php', ['id' => $course]);
                break;
            case "srs status":
                $course = $this->get_course_id($identifier);
                return new moodle_url('/report/grade/srsstatus.php', ['id' => $course]);
                break;
        }
    }
}
