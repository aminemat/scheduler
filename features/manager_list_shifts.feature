Feature: As a manager,
  I want to see the schedule,
  by listing shifts within a specific time period.

  Background:
    Given I have users:
      | id         | name      | email             | role     | password                                                     | phone          |
      | employee_1 | employee1 | employee1@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1111 |
      | employee_2 | employee2 | employee2@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1112 |
      | employee_3 | employee3 | employee3@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1113 |
      | employee_4 | employee4 | employee4@foo.com | employee | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i | (612) 111-1114 |
      | manager_1  | manager   | manager@foo.com   | manager  | $2y$10$VNc/MX2Mi3Um1wydjDJ.jOGjMpQ6TvWuiXFuKWq3bYUh0EblZNN1i |                |
    And I have shifts:
      | id                 | employee_id | manager_id | break | start_time          | end_time            |
      | shift_1_employee_1 | employee_1  | manager_1  | 0.25  | 2016-05-18 14:30:00 | 2016-05-18 20:00:00 |
      | shift_1_employee_2 | employee_2  | manager_1  | 0.25  | 2016-05-18 20:00:00 | 2016-05-19 02:00:00 |
      | shift_2_employee_1 | employee_1  | manager_1  | 0.25  | 2016-05-19 09:00:00 | 2016-05-19 17:00:00 |
      | shift_1_employee_3 | employee_3  | manager_1  | 0.25  | 2016-05-19 12:00:00 | 2016-05-19 18:00:00 |
      | shift_1_employee_4 | employee_4  | manager_1  | 0.5   | 2016-05-20 12:00:00 | 2016-05-20 18:00:00 |
    And I set header "Content-Type" with value "application/json"
    And I set header "Accept" with value "application/json"
    And I login with credentials "manager@foo.com" "foobar"

  Scenario: As a manager,
  I want to see the schedule,
  by listing shifts within a specific time period in a chronological order.
    Given I send a GET request to "/shifts?start_date=2016-05-18&end_date=2016-05-22"
    Then The response validates:
      | property                   | value                         | type    |
      # shift_1_employee_1
      | shifts.0.shift.id          | shift_1_employee_1            | string  |
      | shifts.0.shift.start       | Wed, 18 May 16 14:30:00 +0000 | string  |
      | shifts.0.shift.end         | Wed, 18 May 16 20:00:00 +0000 | string  |
      | shifts.0.shift.employee.id | employee_1                    | string  |
      # shift_1_employee_2
      | shifts.1.shift.id          | shift_1_employee_2            | string  |
      | shifts.1.shift.start       | Wed, 18 May 16 20:00:00 +0000 | string  |
      | shifts.1.shift.end         | Thu, 19 May 16 02:00:00 +0000 | string  |
      | shifts.1.shift.employee.id | employee_2                    | string  |
      # shift_2_employee_1
      | shifts.2.shift.id          | shift_2_employee_1            | string  |
      | shifts.2.shift.start       | Thu, 19 May 16 09:00:00 +0000 | string  |
      | shifts.2.shift.end         | Thu, 19 May 16 17:00:00 +0000 | string  |
      | shifts.2.shift.employee.id | employee_1                    | string  |
      # shift_1_employee_3
      | shifts.3.shift.id          | shift_1_employee_3            | string  |
      | shifts.3.shift.start       | Thu, 19 May 16 12:00:00 +0000 | string  |
      | shifts.3.shift.end         | Thu, 19 May 16 18:00:00 +0000 | string  |
      | shifts.3.shift.employee.id | employee_3                    | string  |
      # shift_1_employee_3
      | shifts.4.shift.id          | shift_1_employee_4            | string  |
      | shifts.4.shift.start       | Fri, 20 May 16 12:00:00 +0000 | string  |
      | shifts.4.shift.end         | Fri, 20 May 16 18:00:00 +0000 | string  |
      | shifts.4.shift.employee.id | employee_4                    | string  |
      # There should be 5 shifts total
      | metadata.count             | 5                             | integer |  
