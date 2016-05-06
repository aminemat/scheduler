Feature: As an employee,
  I want to be able to contact my managers,
  by seeing manager contact information for my shifts.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | manager_1  | manager   | manager1@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (952) 952 952  |
      | manager_2  | manager   | manager2@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |                |
    And I have shifts:
      | id      | employee_id | manager_id | break | start_time     | end_time        |
      | shift_1 | employee_1  | manager_1  | 0.25  | now + 1 hour   | now + 5 hours   |
      | shift_2 | employee_1  | manager_2  | 0.5   | tomorrow 9 a.m | tomorrow 12 p.m |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "employee1@foo.com" "foobar"

  Scenario: As an employee,
  I want to be able to contact my managers,
  by seeing manager contact information for my shifts.
    Given I send a GET request to "/shifts"
    Then the response code should be 200
    And The response validates:
      | property                     | value            | type   |
      | shifts.0.shift.id            | shift_1          | string |
      | shifts.0.shift.manager.email | manager1@foo.com | string |
      | shifts.0.shift.manager.phone | (952) 952 952    | string |
      | shifts.1.shift.manager.phone |                  | string |
      | shifts.0.shift.manager.email | manager1@foo.com | string |
