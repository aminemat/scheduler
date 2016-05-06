Feature: As a manager,
  I want to contact an employee,
  by seeing employee details.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | manager_1  | manager   | manager1@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (952) 952 952  |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "manager1@foo.com" "foobar"

  Scenario: As a manager,
  I want to contact an employee,
  by seeing employee details.
    Given I send a GET request to "/employee/employee_1"
    Then The response validates:
      | property       | value             | type   |
      | employee.id    | employee_1        | string |
      | employee.name  | employee1         | string |
      | employee.email | employee1@foo.com | string |
      | employee.phone | (612) 111-1111    | string |
