Feature: As an employee,
  I want to know how much I worked,
  by being able to get a summary of hours worked for each week.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | manager_1  | manager   | manager@foo.com   | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |                |
    And I have shifts:
      | id      | employee_id | manager_id | break | start_time          | end_time            |
      #3 hour shift
      | shift_1 | employee_1  | manager_1  | 0.25  | 2016-05-02 09:00:00 | 2016-05-02 12:00:00 |
      #3 hour shift
      | shift_2 | employee_1  | manager_1  | 0.25  | 2016-05-09 09:00:00 | 2016-05-09 12:00:00 |
      #8 hour shift
      | shift_3 | employee_1  | manager_1  | 0.5   | 2016-05-10 09:00:00 | 2016-05-10 17:00:00 |
      #6 hour shift
      | shift_4 | employee_1  | manager_1  | 0.5   | 2016-05-19 14:00:00 | 2016-05-19 20:00:00 |
      #8.5 hour shift
      | shift_5 | employee_1  | manager_1  | 0.5   | 2016-05-20 09:00:00 | 2016-05-20 17:30:00 |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "employee1@foo.com" "foobar"

  Scenario: As an employee,
  I want to know when I am working,
  by being able to see all of the shifts assigned to me.
    Given I send a GET request to "/worked-hours"
    Then the response code should be 200
    And The response validates:
      | property        | value     | type    |
      | employee        | employee1 | string  |
      | summary.0.year  | 2016      | string  |
      | summary.0.week  | 18        | string  |
      | summary.0.hours | 3         | integer |
      | summary.1.year  | 2016      | string  |
      | summary.1.week  | 19        | string  |
      | summary.1.hours | 11        | integer |
      | summary.2.year  | 2016      | string  |
      | summary.2.week  | 20        | string  |
      | summary.2.hours | 14.5      | double  |