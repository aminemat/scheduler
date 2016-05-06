Feature: As an employee,
  I want to know who I am working with,
  by being able to see the employees that are working during the same time period as me.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | employee_2 | employee2 | employee2@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-2222 |
      | manager_1  | manager   | manager@foo.com   | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |                |
    And I have shifts:
      | id      | employee_id | manager_id | break | start_time     | end_time        |
      | shift_1 | employee_1  | manager_1  | 0.25  | now + 1 hour   | now + 5 hours   |
      | shift_2 | employee_1  | manager_1  | 0.5   | tomorrow 9 a.m | tomorrow 12 p.m |
      | shift_3 | employee_2  | manager_1  | 1     | tomorrow 9 a.m | tomorrow 5 p.m  |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "employee1@foo.com" "foobar"

  Scenario: As an employee,
  I want to know who I am working with,
  by being able to see the employees that are working during the same time period as me.
    Given I send a GET request to "/shifts?coworkers"
    Then the response code should be 200
    And The response validates:
      | property                   | value      | type   |
      | shifts.0.shift.id          | shift_3    | string |
      | shifts.0.shift.start       | <skip>     | string |
      | shifts.0.shift.end         | <skip>     | string |
      | shifts.0.shift.break       | 1          | string |
      | shifts.0.shift.employee.id | employee_2 | string |