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
 * Display user grade reports for a course (totals)
 *
 * @package    report
 * @subpackage grade
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/report/grade/locallib.php');

$id = required_param('id',PARAM_INT);       // course id
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

$PAGE->set_url('/report/grade/index.php', array('id'=>$id));
$PAGE->set_pagelayout('report');

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/grade:view', $context);

// Trigger an grade report viewed event.
$event = \report_grade\event\grade_report_viewed::create(array('context' => $context));
$event->trigger();

$strgradereport  = get_string('pluginname', 'report_grade');
$strfirst        = get_string('firstname', 'report_grade');
$strlast         = get_string('surname', 'report_grade');
$strid           = get_string('idnumber', 'report_grade');
$strgrade        = get_string('grade');

$PAGE->set_title($course->shortname .': '. $strgradereport);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($course->fullname));

// Set up static column headers
$table = new html_table();
$table->attributes['class'] = 'generaltable boxaligncenter';
$table->cellpadding = 5;
$table->id = 'gradetable';
$table->head = array($strfirst, $strlast, $strid);

require_once($CFG->dirroot.'/grade/export/lib.php');
global $DB;
$assigns = $DB->get_records_sql('SELECT iteminstance, itemname FROM {grade_items} where courseid = ? AND itemmodule = ? AND idnumber != ?', array($course->id,'assign', ''));
if(count($assigns) > 0){
  $users = get_enrolled_users(context_course::instance($course->id), 'mod/assign:submit', 0, 'u.*', 'firstname');
  $a = implode(",",array_keys($assigns));
  $a = "(" . $a . ")";

  $sql_double = "SELECT g.id, d.assignment, a.grade, g.userid, d.first_grade, d.second_grade, a.name, a.grade scale
                FROM {assignfeedback_doublemark} d
                JOIN {assign_grades} g ON g.assignment = d.assignment AND g.id = d.grade
                JOIN {assign} a ON a.id = g.assignment
                WHERE d.assignment IN $a";

  $doublemarks = $DB->get_records_sql($sql_double);

  $sql_sample = "SELECT g.id, g.userid, s.assignment, s.sample
                FROM mdl_assignfeedback_sample s
                LEFT JOIN mdl_assign_grades g ON g.assignment = s.assignment AND g.id = s.grade
                WHERE s.assignment IN $a";
  $sample = $DB->get_records_sql($sql_sample);
  $allgrades = array();

  foreach($assigns as $k => $v){
    //Set up assignment column headers
    $conf_double = $DB->get_record('assign_plugin_config', array('assignment'=>$v->iteminstance, 'plugin'=>'doublemark', 'subtype'=>'assignfeedback', 'name'=>'enabled'));
    $conf_sample = $DB->get_record('assign_plugin_config', array('assignment'=>$v->iteminstance, 'plugin'=>'sample', 'subtype'=>'assignfeedback', 'name'=>'enabled'));

    if($conf_double->name == "enabled" && $conf_double->value == 1){
      $table->head[] = ($v->itemname . get_string('firstmark', 'report_grade'));
      $table->head[] = ($v->itemname . get_string('secondmark', 'report_grade'));
    }
    $table->head[] = ($v->itemname . get_string('finalgrade', 'report_grade'));
   if($conf_sample->name == "enabled" && $conf_sample->value == 1){
      $table->head[] = ($v->itemname . get_string('sample', 'report_grade'));
   }
    $allgrades[] = grade_get_grades($course->id, 'mod', 'assign', $v->iteminstance, array_keys($users));
  }

  foreach($users as $ku => $vu){
    $row = new html_table_row();
    $cell1 = new html_table_cell($vu->firstname);
    $cell2 = new html_table_cell($vu->lastname);
    $cell3 = new html_table_cell($vu->idnumber);
    $row->cells = array($cell1, $cell2, $cell3);

    foreach($assigns as $k => $v){

      $conf_double = $DB->get_record('assign_plugin_config', array('assignment'=>$v->iteminstance, 'plugin'=>'doublemark', 'subtype'=>'assignfeedback', 'name'=>'enabled'));
      $conf_sample = $DB->get_record('assign_plugin_config', array('assignment'=>$v->iteminstance, 'plugin'=>'sample', 'subtype'=>'assignfeedback', 'name'=>'enabled'));
      foreach($allgrades as $kg => $vg){
        foreach($vg->items as $ki => $vi){
        if($vi->iteminstance == $v->iteminstance){
          $userdoublemarks = get_doublemarks($doublemarks, $v->iteminstance, $vu->id);
          $usersample = get_sample($sample, $v->iteminstance, $vu->id);

          if($conf_double->name == "enabled" && $conf_double->value == 1){
            if(!empty($userdoublemarks)){
              $row->cells[] = new html_table_cell(convert_grade_report($userdoublemarks['scale'], $userdoublemarks['first']));
              $row->cells[] = new html_table_cell(convert_grade_report($userdoublemarks['scale'],$userdoublemarks['second']));
            }else{
              $row->cells[] = new html_table_cell();
              $row->cells[] = new html_table_cell();
            }
          }

          if($vi->grades[$vu->id]->str_grade == '-'){
            $row->cells[] = new html_table_cell();
          }else{
            $row->cells[] = new html_table_cell($vi->grades[$vu->id]->str_grade);
          }
          if($conf_sample->name == "enabled" && $conf_sample->value == 1){
            if(!empty($sample)){
              $row->cells[] = new html_table_cell(get_sample($sample, $v->iteminstance, $vu->id));
            }else{
              $row->cells[] = new html_table_cell();
            }
          }
        }
      }
    }
  } $table->data[] = $row;
}
  echo html_writer::table($table);
}else{
  echo "<br>";
  echo $OUTPUT->notification( get_string('noassignments', 'report_grade'), \core\output\notification::NOTIFY_INFO);
}

echo "<input type='button' id='print_button'onClick='window.print()'' value='Print this report'/>";

echo $OUTPUT->footer();
