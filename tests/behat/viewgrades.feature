@report @javascript @report_grade @sol
Feature: See grades for students
    As a grader
    In order to view all grades given for assignments
    I need to see all the assignments and grades for all students.

    Background:
        Given the following "courses" exist:
            | shortname | fullname | idnumber       |
            | C1        | Course 1 | ABC101_1234546 |
        And the following "activity" exists:
            | activity                            | assign                 |
            | course                              | C1                     |
            | name                                | Not Quercus 1 (50%)    |
            | intro                               | Submit your assignment |
            | idnumber                            |                        |
            | assignfeedback_comments_enabled     | 1                      |
            | assignfeedback_doublemark_enabled   | 1                      |
            | assignfeedback_sample_enabled       | 1                      |
            | markingworkflow                     | 1                      |
            | assignsubmission_onlinetext_enabled | 1                      |
            | scale                               | 1                      |
            | blindmarking                        | 1                      |
        And the following "users" exist:
            | username  | firstname | lastname | email                |
            | student1  | Student   | 1        | student1@example.com |
            | student2  | Student   | 2        | student2@example.com |
            | teacher1  | Teacher   | 1        | teacher1@example.com |
        And the following "course enrolments" exist:
            | user      | course | role    |
            | student1  | C1     | student |
            | student2  | C1     | student |
            | teacher1  | C1     | teacher |
    
    @javascript
    Scenario: No valid assignments to report on
        When I log in as "teacher1"
        And I am on "Course 1" course homepage
        And I navigate to "Grade report" in current page administration
        And I should see "Either there are no Quercus assignments in this unit or they haven't been set up yet."

    @javascript
    Scenario: No grades to report
        Given the following "activity" exists:
            | activity                            | assign                 |
            | course                              | C1                     |
            | name                                | Report 1 (50%)         |
            | intro                               | Submit your assignment |
            | idnumber                            | 2021_REPORT1           |
            | assignfeedback_comments_enabled     | 1                      |
            | assignfeedback_doublemark_enabled   | 1                      |
            | assignfeedback_sample_enabled       | 1                      |
            | markingworkflow                     | 1                      |
            | assignsubmission_onlinetext_enabled | 1                      |
            | scale                               | 1                      |
            | blindmarking                        | 1                      |
        And the following "activity" exists:
            | activity                            | assign                   |
            | course                              | C1                       |
            | name                                | Presentation 1 (50%)     |
            | intro                               | Submit your presentation |
            | idnumber                            | 2021_PRES1               |
            | assignfeedback_comments_enabled     | 1                        |
            | assignfeedback_doublemark_enabled   | 1                        |
            | assignfeedback_sample_enabled       | 1                        |
            | markingworkflow                     | 1                        |
            | assignsubmission_onlinetext_enabled | 1                        |
            | scale                               | 1                        |
            | blindmarking                        | 1                        |
        And the following "mod_assign > submissions" exist:
            | assign        | user     | onlinetext                |
            | 2021_REPORT1  | student1 | I'm the first submission  |
            | 2021_PRES1    | student1 | I'm the second submission |
            | 2021_REPORT1  | student2 | I'm the third submission  |
            | 2021_PRES1    | student2 | I'm the forth submission  |
        Given I log in as "teacher1"
        And I am on "Course 1" course homepage
        And I navigate to "Grade report" in current page administration
        Then the following should exist in the "gradetable" table:
            | Firstname | Surname | Report 1 (50%) First mark | Report 1 (50%) Second mark | Report 1 (50%) Final grade | Report 1 (50%) Sample | Presentation 1 (50%) First mark | Presentation 1 (50%) Second mark | Presentation 1 (50%) Final grade |Presentation 1 (50%) Sample |
            | Student   | 1       |                           |                            |                            |                       |                                 |                                  |                                  |                            |
            | Student   | 2       |                           |                            |                            |                       |                                 |                                  |                                  |                            |
        And the following should not exist in the "gradetable" table:
            | Firstname | Surname | Not Quercus 1 (50%) First mark |
        