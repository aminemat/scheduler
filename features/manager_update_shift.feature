Feature: As a manager,
  I want to be able to change a shift,
  by updating the time details.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | manager_1  | manager   | manager1@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (952) 952 952  |
    And I have shifts:
      | id      | employee_id | manager_id | break | start_time                    | end_time                      |
      | shift_1 | employee_1  | manager_1  | 0.25  | Wed, 18 May 16 14:30:00 -0500 | Wed, 18 May 16 20:30:00 -0500 |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "manager1@foo.com" "foobar"

  Scenario: As a manager,
  I want to be able to change a shift,
  by updating the time details.
    Given I send a PUT request to "/shifts/shift_1" with body:
"""
  {
    "start_time": "2016-05-18 16:00:00.000000", 
    "end_time": "2016-05-18 18:30:00.000000"
  }
"""
    Then The response validates:
      | property    | value                         | type   |
      | shift.id    | shift_1                       | string |
      | shift.start | Wed, 18 May 16 16:00:00 -0500 | string |
      | shift.end   | Wed, 18 May 16 18:30:00 -0500 | string |