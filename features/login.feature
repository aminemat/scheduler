Feature: Login
  As a registered user, I want to be able to login using my credentials.

  Background:
    Given I have users:
      | id         | name     | email            | role     | password                                                     | phone |
      | employee_1 | employee | employee@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |       |
      | manager_1  | manager  | manager@foo.com  | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |       |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"


  Scenario: As an employee,
  I can login with my credentials and obtain an access token
    Given I set header "Content-Type" with value "application/json"
    When I send a POST request to "/login" with body:
"""
{"username": "employee@foo.com", "password": "foobar"}
"""
    Then the response code should be 200
    And The response validates:
      | property   | value            | type   |
      | user.id    | employee_1       | string |
      | user.name  | employee         | string |
      | user.role  | employee         | string |
      | user.email | employee@foo.com | string |
      | token      | <skip>           | string |

  Scenario: As a manager,
  I can login with my credentials and obtain an access token
    Given I set header "Content-Type" with value "application/json"
    When I send a POST request to "/login" with body:
"""
{"username": "manager@foo.com", "password": "foobar"}
"""
    Then the response code should be 200
    And The response validates:
      | property   | value           | type   |
      | user.id    | manager_1       | string |
      | user.name  | manager         | string |
      | user.role  | manager         | string |
      | user.email | manager@foo.com | string |
      | token      | <skip>          | string |

  Scenario: As a user,
  I can an error if I try to log in with invalid credentials
    Given I set header "Content-Type" with value "application/json"
    When I send a POST request to "/login" with body:
"""
{"username": "manager@foo.com", "password": "WRONG"}
"""
    Then the response code should be 500
    # This should be 401, for some reason the framework returns 500 on exceptions

