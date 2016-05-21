# Scheduler API


[![Build Status](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/?branch=master)
[![Code Climate](https://codeclimate.com/github/aminemat/scheduler/badges/gpa.svg)](https://codeclimate.com/github/aminemat/scheduler)

A shift scheduling application exposing an API.
Used as playground to test the [Equip](https://github.com/equip/framework) framework
and all the things [PSR-7](http://www.php-fig.org/psr/psr-7/) and [ADR](https://github.com/pmjones/adr).

## User stories

The application implements this user stories, each story is validated by a BDD style test.

- [X] As an employee, I want to know when I am working, by being able to see all of the shifts assigned to me. [test](features/employee_shift_list.feature)
- [X] As an employee, I want to know who I am working with, by being able to see the employees that are working during the same time period as me. [test](features/employee_coworkers_shifts.feature)
- [X] As an employee, I want to know how much I worked, by being able to get a summary of hours worked for each week. [test](features/employee_work_summary.feature)
- [X] As an employee, I want to be able to contact my managers, by seeing manager contact information for my shifts. [test](features/employee_manager_contact_detail.feature)

- [X] As a manager, I want to schedule my employees, by creating shifts for any employee. [test](features/manager_schedule_shift.feature)
- [X] As a manager, I want to see the schedule, by listing shifts within a specific time period. [test](features/manager_list_shifts.feature)
- [X] As a manager, I want to be able to change a shift, by updating the time details. [test](features/manager_update_shift.feature)
- [X] As a manager, I want to be able to assign a shift, by changing the employee that will work a shift. [test](features/manager_assign_shift.feature)
- [X] As a manager, I want to contact an employee, by seeing employee details. [test](features/manager_employee_detail.feature)

## Install


``` bash
$ composer install
```

## Run the test suite
### Unit tests

``` php
bin/phpunit
```

### Functional tests

To run the functional test suite you need access to a mysql database.

- Run the test server
```
./start-server.sh
```
this command will run the web server bundled with PHP on port 8000

- Copy the behat configuration file
``` php
cp behat.yml.dist behat.yml
```
- Update ```behat.yml``` with your database URL (replace ```DATABASE_URL``` by ```mysql://<USER>:<PASSWORD>@<HOST>```)



## API Docs

Coming soon

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
