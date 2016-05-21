Feature: As a manager,
  I want to schedule my employees,
  by creating shifts for any employee.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | manager_1  | manager   | manager1@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (952) 952 952  |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "manager1@foo.com" "foobar"

  Scenario: As a manager,
  I want to schedule my employees,
  by creating shifts for any employee.
    Given I send a POST request to "/shifts" with body:
"""
  {
    "employee_id": "employee_1", 
    "start_time": "Fri, 07 Mar 2014 08:30:00",
    "end_time": "Fri, 07 Mar 2014 17:30:00",
    "break": "0.5"
    }
"""
    Then The response validates:
      | property             | value                         | type   |
      | shift.id             | <skip>                        | string |
      | shift.start          | Fri, 07 Mar 14 08:30:00 +0000 | string |
      | shift.end            | Fri, 07 Mar 14 17:30:00 +0000 | string |
      | shift.break          | 0.5                           | string |
      | shift.manager.id     | manager_1                     | string |
      | shift.manager.name   | manager                       | string |
      | shift.manager.email  | manager1@foo.com              | string |
      | shift.manager.phone  | (952) 952 952                 | string |
      | shift.employee.id    | employee_1                    | string |
      | shift.employee.name  | employee1                     | string |
      | shift.employee.email | employee1@foo.com             | string |
      | shift.employee.phone | (612) 111-1111                | string |

  Scenario: As a manager,
  If I schedule a shift for an invalid employee,
  I get a 400 error
    Given I send a POST request to "/shifts" with body:
"""
  {
    "employee_id": "foobar", 
    "start_time": "Fri, 07 Mar 2014 08:30:00",
    "end_time": "Fri, 07 Mar 2014 17:30:00",
    "break": "0.5"
    }
"""
    Then the response code should be 400


  Scenario: As a manager,
  If I schedule a shift with a start date preceding the end date,
  I get a 400 error
    Given I send a POST request to "/shifts" with body:
"""
  {
    "employee_id": "employee_1", 
    "start_time": "Fri, 07 Mar 2014 05:30:00",
    "end_time": "Fri, 07 Mar 2014 04:30:00",
    "break": "0.5"
    }
"""
    Then the response code should be 400
