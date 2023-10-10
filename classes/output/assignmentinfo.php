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
 * Assignment info
 *
 * @package   report_grade
 * @author    Mark Sharp <mark.sharp@solent.ac.uk>
 * @copyright 2022 Solent University {@link https://www.solent.ac.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_grade\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Outputs sitting and board date for assignment
 */
class assignmentinfo implements renderable, templatable {
    /**
     * Data about the assignment
     *
     * @var stdClass
     */
    private $data;
    /**
     * Constructor
     *
     * @param stdClass $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     *
     * @param renderer_base $output
     * @return stdClass Context for template
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        if ($this->data->source == 'quercus') {
            if ($this->data->assignment->sitting) {
                $data->sitting = $this->data->assignment->sitting;
            }
            if ($this->data->assignment->externaldate) {
                $data->externaldate = userdate($this->data->assignment->externaldate);
            }
        }
        return $data;
    }
}
