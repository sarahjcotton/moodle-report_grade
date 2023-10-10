@report @report_grade @sol @javascript
Feature: See the status of grades uploaded to Student Records
  As a grader
  In order to check all grades have uploaded to SRS
  I need to see the status for each student on each assignment

  Background:
    Given the following "courses" exist:
    | fullname                | shortname             | idnumber              |
    | Making widgets (ABC101) | ABC101_A_SEM1_2023/24 | ABC101_A_SEM1_2023/24 |
    | Making widgets (ABC101) | ABC101_123456789      | ABC101_123456789      |
    And I log in as "admin"
    And the solent gradescales are setup
    And the following config values are set as admin:
    | config                   | value  | plugin                    |
    | theme                    | solent |                           |
    | blindmarking             | 1      | assign                    |
    | markingworkflow          | 1      | assign                    |
    | default                  | 1      | assignfeedback_misconduct |
    | default                  | 1      | assignfeedback_doublemark |
    | cutoffinterval           | 1      | local_quercus_tasks       |
    | cutoffintervalsecondplus | 1      | local_quercus_tasks       |
    | gradingdueinterval       | 2      | local_quercus_tasks       |
    And the following SITS assignment exists:
    | sitsref         | ABC101_A_SEM1_2023/24_ABC10101_001_0 |
    | course          | ABC101_A_SEM1_2023/24                |
    | title           | Report 1 (25%)                       |
    | weighting       | 25                                   |
    | duedate         | ## 5 May 2023 16:00:00 ##            |
    | assessmentcode  | ABC10101                             |
    | assessmentname  | Report 1                             |
    | sequence        | 001                                  |
    | availablefrom   | 0                                    |
    | reattempt       | 0                                    |
    | grademarkexempt | 0                                    |
    And the following SITS assignment exists:
    | sitsref         | ABC101_A_SEM1_2023/24_ABC10101_002_0 |
    | course          | ABC101_A_SEM1_2023/24                |
    | title           | Report 2 (25%)                       |
    | weighting       | 25                                   |
    | duedate         | ## 2 June 2023 16:00:00 ##           |
    | assessmentcode  | ABC10101                             |
    | assessmentname  | Report 2                             |
    | sequence        | 001                                  |
    | availablefrom   | ## 2 June 2023 09:00:00 ##           |
    | reattempt       | 0                                    |
    | grademarkexempt | 1                                    |
    And the following Quercus assignment exists:
    | course                | ABC101_123456789          |
    | weighting             | .25                       |
    | assessmentCode        | Report1                   |
    | assessmentDescription | Report 1                  |
    | dueDate               | ## 5 May 2023 16:00:00 ## |
    | academicYear          | 2022                      |
    And the following Quercus assignment exists:
    | course                | ABC101_123456789           |
    | weighting             | .25                        |
    | assessmentCode        | Report2                    |
    | assessmentDescription | Report 2                   |
    | dueDate               | ## 6 June 2023 16:00:00 ## |
    | academicYear          | 2022                       |
    And the following "roles" exist:
    | shortname    | name          | archetype      |
    | moduleleader | Module leader | editingteacher |
    And I set the following system permissions of "Module leader" role:
    | capability                  | permission |
    | local/solsits:releasegrades | Allow      |
    And the following "users" exist:
    | username      | firstname | lastname | email                     | idnumber |
    | student1      | Student   | 1        | student1@example.com      | 12345671 |
    | student2      | Student   | 2        | student2@example.com      | 12345672 |
    | student3      | Student   | 3        | student3@example.com      | 12345673 |
    | student4      | Student   | 4        | student4@example.com      | 12345674 |
    | student5      | Student   | 5        | student5@example.com      | 12345675 |
    | student6      | Student   | 6        | student6@example.com      | 12345676 |
    | student7      | Student   | 7        | student7@example.com      | 12345677 |
    | student8      | Student   | 8        | student8@example.com      | 12345678 |
    | student9      | Student   | 9        | student9@example.com      | 12345679 |
    | student0      | Student   | 0        | student0@example.com      | 12345670 |
    | teacher1      | Teacher   | 1        | teacher1@example.com      | X1234567 |
    | moduleleader1 | Leader    | 1        | moduleleader1@example.com | X1234568 |
    And the following "course enrolments" exist:
    | user          | course                | role           |
    | student1      | ABC101_A_SEM1_2023/24 | student        |
    | student2      | ABC101_A_SEM1_2023/24 | student        |
    | student3      | ABC101_A_SEM1_2023/24 | student        |
    | student4      | ABC101_A_SEM1_2023/24 | student        |
    | student5      | ABC101_A_SEM1_2023/24 | student        |
    | student6      | ABC101_A_SEM1_2023/24 | student        |
    | student7      | ABC101_A_SEM1_2023/24 | student        |
    | student8      | ABC101_A_SEM1_2023/24 | student        |
    | student9      | ABC101_A_SEM1_2023/24 | student        |
    | student0      | ABC101_A_SEM1_2023/24 | student        |
    | teacher1      | ABC101_A_SEM1_2023/24 | editingteacher |
    | moduleleader1 | ABC101_A_SEM1_2023/24 | moduleleader   |
    | student1      | ABC101_123456789      | student        |
    | student2      | ABC101_123456789      | student        |
    | student3      | ABC101_123456789      | student        |
    | student4      | ABC101_123456789      | student        |
    | student5      | ABC101_123456789      | student        |
    | student6      | ABC101_123456789      | student        |
    | student7      | ABC101_123456789      | student        |
    | student8      | ABC101_123456789      | student        |
    | student9      | ABC101_123456789      | student        |
    | student0      | ABC101_123456789      | student        |
    | teacher1      | ABC101_123456789      | editingteacher |
    | moduleleader1 | ABC101_123456789      | moduleleader   |
    And the following "mod_assign > submissions" exist:
    | assign                               | user      | onlinetext                          |
    # SITS.
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student1  | I'm the student 1 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student2  | I'm the student 2 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student3  | I'm the student 3 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student4  | I'm the student 4 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student5  | I'm the student 5 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student6  | I'm the student 6 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student7  | I'm the student 7 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student8  | I'm the student 8 first submission  |
    | ABC101_A_SEM1_2023/24_ABC10101_001_0 | student9  | I'm the student 9 first submission  |
    # student0 makes no submission, and will not be marked.
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student1  | I'm the student 1 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student2  | I'm the student 2 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student3  | I'm the student 3 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student4  | I'm the student 4 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student5  | I'm the student 5 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student6  | I'm the student 6 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student7  | I'm the student 7 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student8  | I'm the student 8 second submission |
    | ABC101_A_SEM1_2023/24_ABC10101_002_0 | student9  | I'm the student 9 second submission |
    # Quercus.
    | 2022_Report1                         | student1  | I'm the student 1 first submission  |
    | 2022_Report1                         | student2  | I'm the student 2 first submission  |
    | 2022_Report1                         | student3  | I'm the student 3 first submission  |
    | 2022_Report1                         | student4  | I'm the student 4 first submission  |
    | 2022_Report1                         | student5  | I'm the student 5 first submission  |
    | 2022_Report1                         | student6  | I'm the student 6 first submission  |
    | 2022_Report1                         | student7  | I'm the student 7 first submission  |
    | 2022_Report1                         | student8  | I'm the student 8 first submission  |
    | 2022_Report1                         | student9  | I'm the student 9 first submission  |
    # student0 makes no submission, and will not be marked.
    | 2022_Report2                         | student1  | I'm the student 1 second submission |
    | 2022_Report2                         | student2  | I'm the student 2 second submission |
    | 2022_Report2                         | student3  | I'm the student 3 second submission |
    | 2022_Report2                         | student4  | I'm the student 4 second submission |
    | 2022_Report2                         | student5  | I'm the student 5 second submission |
    | 2022_Report2                         | student6  | I'm the student 6 second submission |
    | 2022_Report2                         | student7  | I'm the student 7 second submission |
    | 2022_Report2                         | student8  | I'm the student 8 second submission |
    | 2022_Report2                         | student9  | I'm the student 9 second submission |
    # Double mark the SITS assignments.
    And I am on the "ABC101_A_SEM1_2023/24_ABC10101_001_0" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    Then I click on "Grade" "link" in the "12345671" "table_row"
    And I set the field "First grade" to "A1"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 1!"
    # Get inconsistent web service errors. Try waiting.
    And I wait until the page is ready
    And I press "Save changes"
    And I set the field "First grade" to "B2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 2!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "C3"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 3!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "D1"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 4!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "F2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 5!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "S"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 6!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "N"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 7!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "A2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 8!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "B3"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 9!"
    And I press "Save changes"
    # Module leader now marks and agrees final grade.
    And I am on the "ABC101_A_SEM1_2023/24_ABC10101_001_0" "assign activity" page logged in as moduleleader1
    And I follow "View all submissions"
    Then I click on "Grade" "link" in the "12345671" "table_row"
    And I set the field "Second grade" to "A1"
    And I set the field "Agreed grade" to "A1"
    And I set the field "Marking workflow state" to "Marking complete"
    # Get inconsistent web service errors. Try waiting.
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "B2"
    And I set the field "Agreed grade" to "B2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "C3"
    And I set the field "Agreed grade" to "C3"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "D1"
    And I set the field "Agreed grade" to "D1"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "F2"
    And I set the field "Agreed grade" to "F2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "S"
    And I set the field "Agreed grade" to "S"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "N"
    And I set the field "Agreed grade" to "N"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "A2"
    And I set the field "Agreed grade" to "A2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "B3"
    And I set the field "Agreed grade" to "B3"
    And I set the field "Marking workflow state" to "Marking complete"
    And I press "Save changes"
    # Double mark the Quercus assignments.
    And I am on the "2022_Report1" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    Then I click on "Grade" "link" in the "12345671" "table_row"
    And I set the field "First grade" to "A1"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 1!"
    # Get inconsistent web service errors. Try waiting.
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "B2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 2!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "C3"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 3!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "D1"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 4!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "F2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 5!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "S"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 6!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "N"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 7!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "A2"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 8!"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "First grade" to "B3"
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Feedback comments" to "Great job no. 9!"
    And I press "Save changes"
    # Module leader now marks and agrees final grade.
    And I am on the "2022_Report1" "assign activity" page logged in as moduleleader1
    And I follow "View all submissions"
    Then I click on "Grade" "link" in the "12345671" "table_row"
    And I set the field "Second grade" to "A1"
    And I set the field "Agreed grade" to "A1"
    And I set the field "Marking workflow state" to "Marking complete"
    # Get inconsistent web service errors. Try waiting.
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "B2"
    And I set the field "Agreed grade" to "B2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "C3"
    And I set the field "Agreed grade" to "C3"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "D1"
    And I set the field "Agreed grade" to "D1"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "F2"
    And I set the field "Agreed grade" to "F2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "S"
    And I set the field "Agreed grade" to "S"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "N"
    And I set the field "Agreed grade" to "N"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "A2"
    And I set the field "Agreed grade" to "A2"
    And I set the field "Marking workflow state" to "Marking complete"
    And I wait until the page is ready
    And I press "Save and show next"
    And I set the field "Second grade" to "B3"
    And I set the field "Agreed grade" to "B3"
    And I set the field "Marking workflow state" to "Marking complete"
    And I press "Save changes"

  Scenario: Assignments are listed in srs status page, but no grades have been released
    Given I log in as "teacher1"
    And I am on the "ABC101_A_SEM1_2023/24" "report_grade > Grade report" page
    When I follow "Marks upload status"
    Then I should see "Report 1 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Report 2 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Marks upload status for assignment \"Report 1 (25%)\""
    And I should see "No released grades to display"
    When I click on "Report 2 (25%)" "link" in the ".marksupload-assignment-list" "css_element"
    Then I should see "No released grades to display"
    And I should see "Marks upload status for assignment \"Report 2 (25%)\""
    And I am on the "ABC101_123456789" "report_grade > Grade report" page
    When I follow "Marks upload status"
    Then I should see "Report 1 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Report 2 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Marks upload status for assignment \"Report 1 (25%)\""
    And I should see "No released grades to display"
    When I click on "Report 2 (25%)" "link" in the ".marksupload-assignment-list" "css_element"
    Then I should see "No released grades to display"
    And I should see "Marks upload status for assignment \"Report 2 (25%)\""

  Scenario: SITS assignment grades are released
    # Release the assignments.
    Given I am on the "ABC101_A_SEM1_2023/24_ABC10101_001_0" "assign activity" page logged in as moduleleader1
    And I follow "View all submissions"
    # Reveal identities before releasing seems to work rather than the other way around.
    And I select "Reveal student identities" from the "Grading action" singleselect
    And I press "Continue"
    And I set the field "selectall" to "1"
    And I set the field "operation" to "Set marking workflow state"
    And I click on "Go" "button" confirming the dialogue
    And I set the field "Marking workflow state" to "Released"
    And I set the field "Notify student" to "No"
    And I press "Save changes"
    And I log in as "admin"
    # Run the task to queue the grades for export.
    And I run the scheduled task "\local_solsits\task\get_new_grades_task"
    And I am on the "ABC101_A_SEM1_2023/24" "report_grade > Grade report" page logged in as moduleleader1
    When I follow "Marks upload status"
    Then I should see "Report 1 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Report 2 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Marks upload status for assignment \"Report 1 (25%)\""
    And the following should exist in the "report_grade-srs_status" table:
    | First name / Last name | Solent grade | Converted grade | Status | Error report |
    | Student 1              | A1           | 100             |        |              |
    | Student 2              | B2           | 65              |        |              |
    | Student 3              | C3           | 52              |        |              |
    | Student 4              | D1           | 48              |        |              |
    | Student 5              | F2           | 20              |        |              |
    | Student 6              | S            | 1               |        |              |
    | Student 7              | N            | 0               |        |              |
    | Student 8              | A2           | 92              |        |              |
    | Student 9              | B3           | 62              |        |              |
    | Student 0              | Unmarked     | 0               |        |              |
    When I click on "Report 2 (25%)" "link" in the ".marksupload-assignment-list" "css_element"
    Then I should see "No released grades to display"
    And I should see "Marks upload status for assignment \"Report 2 (25%)\""
    # Use this in place of running the export grades task as there's no connection.
    And the following SITS grades are stored for "ABC101_A_SEM1_2023/24_ABC10101_001_0":
    | user       | response | message |
    | student1   | SUCCESS  |         |
    | student2   | SUCCESS  |         |
    | student3   | SUCCESS  |         |
    | student4   | SUCCESS  |         |
    | student5   | SUCCESS  |         |
    | student6   | SUCCESS  |         |
    | student7   | SUCCESS  |         |
    | student8   | SUCCESS  |         |
    | student9   | SUCCESS  |         |
    | student0   | SUCCESS  |         |
    And I am on the "ABC101_A_SEM1_2023/24" "report_grade > Grade report" page logged in as moduleleader1
    When I follow "Marks upload status"
    And the following should exist in the "report_grade-srs_status" table:
    | First name / Last name | Solent grade | Converted grade | Status  | Error report |
    | Student 1              | A1           | 100             | SUCCESS |              |
    | Student 2              | B2           | 65              | SUCCESS |              |
    | Student 3              | C3           | 52              | SUCCESS |              |
    | Student 4              | D1           | 48              | SUCCESS |              |
    | Student 5              | F2           | 20              | SUCCESS |              |
    | Student 6              | S            | 1               | SUCCESS |              |
    | Student 7              | N            | 0               | SUCCESS |              |
    | Student 8              | A2           | 92              | SUCCESS |              |
    | Student 9              | B3           | 62              | SUCCESS |              |
    | Student 0              | Unmarked     | 0               | SUCCESS |              |

  Scenario: Quercus assignment grades are released
    # Release the assignments.
    Given I am on the "2022_Report1" "assign activity" page logged in as moduleleader1
    And I follow "View all submissions"
    # Reveal identities before releasing seems to work rather than the other way around.
    And I select "Reveal student identities" from the "Grading action" singleselect
    And I press "Continue"
    And I set the field "selectall" to "1"
    And I set the field "operation" to "Set marking workflow state"
    And I click on "Go" "button" confirming the dialogue
    And I set the field "Marking workflow state" to "Released"
    And I set the field "Notify student" to "No"
    And I press "Save changes"
    And I log in as "admin"
    # Run the task to queue the grades for export.
    And I run the scheduled task "\local_quercus_tasks\task\get_new_grades"
    And I am on the "ABC101_123456789" "report_grade > Grade report" page logged in as moduleleader1
    When I follow "Marks upload status"
    Then I should see "Report 1 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Report 2 (25%)" in the ".marksupload-assignment-list" "css_element"
    And I should see "Marks upload status for assignment \"Report 1 (25%)\""
    And the following should exist in the "report_grade-srs_status" table:
    | First name / Last name | Solent grade | Converted grade | Status | Error report |
    | Student 1              | A1           | 100             |        |              |
    | Student 2              | B2           | 65              |        |              |
    | Student 3              | C3           | 52              |        |              |
    | Student 4              | D1           | 48              |        |              |
    | Student 5              | F2           | 20              |        |              |
    | Student 6              | S            | 1               |        |              |
    | Student 7              | N            | 0               |        |              |
    | Student 8              | A2           | 92              |        |              |
    | Student 9              | B3           | 62              |        |              |
    | Student 0              | Unmarked     | 0               |        |              |
    When I click on "Report 2 (25%)" "link" in the ".marksupload-assignment-list" "css_element"
    Then I should see "No released grades to display"
    And I should see "Marks upload status for assignment \"Report 2 (25%)\""
    # Use this in place of running the export grades task as there's no connection.
    And the following Quercus grades are stored for "2022_Report1":
    | user       | response | processed |
    | student1   | SUCCESS  |           |
    | student2   | SUCCESS  |           |
    | student3   | SUCCESS  |           |
    | student4   | SUCCESS  |           |
    | student5   | SUCCESS  |           |
    | student6   | SUCCESS  |           |
    | student7   | SUCCESS  |           |
    | student8   | SUCCESS  |           |
    | student9   | SUCCESS  |           |
    | student0   | SUCCESS  |           |
    And I am on the "ABC101_123456789" "report_grade > Grade report" page logged in as moduleleader1
    When I follow "Marks upload status"
    And the following should exist in the "report_grade-srs_status" table:
    | First name / Last name | Solent grade | Converted grade | Status  | Error report |
    | Student 1              | A1           | 100             | SUCCESS |              |
    | Student 2              | B2           | 65              | SUCCESS |              |
    | Student 3              | C3           | 52              | SUCCESS |              |
    | Student 4              | D1           | 48              | SUCCESS |              |
    | Student 5              | F2           | 20              | SUCCESS |              |
    | Student 6              | S            | 1               | SUCCESS |              |
    | Student 7              | N            | 0               | SUCCESS |              |
    | Student 8              | A2           | 92              | SUCCESS |              |
    | Student 9              | B3           | 62              | SUCCESS |              |
    | Student 0              | Unmarked     | 0               | SUCCESS |              |


