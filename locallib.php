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
 * This file contains functions used by the grade reports
 *
 * @package    report
 * @subpackage grade
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

function convert_grade_report($scaleid, $grade){

	if($scaleid == 34){ // Solent gradescale
	    $converted = -1;
	    switch ($grade){
        case 18:
                $converted = 'A1'; 	// A1
        break;
        case 17:
                $converted = 'A2';		// A2
                break;
        case 16:
                $converted = 'A3';	// A3
                break;
        case 15:
                $converted = 'A4';	// A4
                break;
        case 14:
                $converted = 'B1';	// B1
                break;
        case 13:
                $converted = 'B2';	// B2
                break;
        case 12:
                $converted = 'B3';	// B3
                break;
        case 11:
                $converted = 'C1';	// C1
                break;
        case 10:
                $converted = 'C2';	// C2
                break;
        case 9:
                $converted = 'C3';	// C3
                break;
        case 8:
                $converted = 'D1';	// D1
                break;
        case 7:
                $converted = 'D2';	// D2
                break;
        case 6:
                $converted = 'D3';	// D3
                break;
        case 5:
                $converted = 'F1';	// F1
                break;
        case 4:
                $converted = 'F2';	// F2
                break;
        case 3:
                $converted = 'F3';	// F3
                break;
        case 2:
                $converted = 'S';		// S
                break;
        case 1:
                $converted = 'N';		// N
                break;
				case NULL:
                $converted = 'N';		// N
                break;
				case -1:
                $converted = '';		// N
                break;
				case '-':
                $converted = '';		// N
                break;
	    }
		}elseif($scaleid == 38){
			if($grade == NULL){
        $converted = 0;
			}else{
				$converted = (int)unformat_float($grade) -1;
			}
		}
    return $converted;
}

function get_doublemarks($doublemarks, $iteminstance, $userid){
  $return = '';
  foreach($doublemarks as $key => $value){
    if($value->userid == $userid && $iteminstance == $value->assignment){
      $return['scale'] = ltrim($value->scale,'-');
      $return['first'] = $value->first_grade;
      $return['second'] = $value->second_grade;
    }
  }
  return $return;
}

function get_sample($sample, $iteminstance, $userid){
  $return = '';
  foreach($sample as $key => $value){
    if($value->userid == $userid && $iteminstance == $value->assignment){
      if($value->sample == 1){
          $return = 'Yes';
      }
    }
  }
  return $return;
}

function get_external_examiner(){
  global $DB, $COURSE;
  $externalexaminer = $DB->get_record_sql("SELECT CONCAT(u.firstname, ' ', u.lastname) name
                                        FROM {user} u
                                        INNER JOIN {role_assignments} ra ON ra.userid = u.id
                                        INNER JOIN {context} ct ON ct.id = ra.contextid
                                        INNER JOIN {course} c ON c.id = ct.instanceid
                                        INNER JOIN {role} r ON r.id = ra.roleid
                                        WHERE r.shortname = ?
                                        AND c.id = ?",
                                        array(get_config('report_grade', 'externalexaminershortname'), $COURSE->id));
    return $externalexaminer;
}

function get_moderators(){
  global $DB, $COURSE;
  $externalexaminer = $DB->get_records_sql("SELECT CONCAT(u.firstname, ' ', u.lastname) name
                                        FROM {user} u
                                        INNER JOIN {role_assignments} ra ON ra.userid = u.id
                                        INNER JOIN {context} ct ON ct.id = ra.contextid
                                        INNER JOIN {course} c ON c.id = ct.instanceid
                                        INNER JOIN {role} r ON r.id = ra.roleid
                                        WHERE r.shortname = ?
                                        AND c.id = ?",
                                        array(get_config('report_grade', 'moderatorshortname'), $COURSE->id));
    return $externalexaminer;
}

function get_ee_form_url(){
	global $DB, $COURSE;
	$dbman = $DB->get_manager();
  if($dbman->table_exists('report_ee')){
		$url = new moodle_url('/report/ee/index.php', array('id'=>$COURSE->id));
		$url = "<p><a href='". $url . "'>" . get_string('reporturl', 'report_grade'). "</a></p>";

		return $url;
	}
		return null;
}
