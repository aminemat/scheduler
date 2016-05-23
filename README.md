# Scheduler API


[![Build Status](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/build.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/aminemat/scheduler/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aminemat/scheduler/?branch=master)
[![Code Climate](https://codeclimate.com/github/aminemat/scheduler/badges/gpa.svg)](https://codeclimate.com/github/aminemat/scheduler)

A shift scheduling application exposing a "REST" API.
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

# Documentation

[API docs](http://aminemat.github.io/slate/)


## Setup

### Install dependencies

``` bash
$ composer install
```

### Create the database

Create a database and import the sql script in [app/resources/db/scheduler.sql](app/resources/db/scheduler.sql)

The dump contains the database structure and 2 test users:

A manager, `username:manager`, `password:manager`

An employee, `username:employee`, `password:employee`

### Create a configuration file

Copy the example config file:

```
cp config.json.dist config.json
```

Replace the Database credentials and the JWT key by your own settings.

### Point your webserver to the `/web` directory
Or simply run the PHP built-in webserver ```php -S localhost:8000  web/index.php```

## Run the tests
### Unit tests

``` php
bin/phpunit
```

### Functional tests

- Create the behat configuration file
```
cp behat.yml.dist behat.yml
```

- Update the database URL and base path, no need to create the database, behat will re-create it before every test scenario.

- In order to not delete your production database and get fired! the test database is hard coded to be named "scheduler-test",
so you need to point your application toward the same DB.

You can do this by updating the database URL in `config.json`
  This is what it looks like in `config.json`
```
mysql://DB_USER:DB_PASS@DB_HOST/scheduler-test
```

- Run the test server
```
php -S localhost:8000  web/index.php
```
this command will run the web server bundled with PHP on port 8000


- Run the tests
```
bin/behat
```

# Postman collection

Love postman? Import [this collection](postman_collection.json) and rock on!

## Limitations:

This repository servers a temporary purpose and is a playground for new ideas so please
use it at your own risk as the application is lacking few things in many areas due to lack of time:

- Functional test suite is naive: Normally you'd test all edge cases or most of them, I did not.
- Unit test coverage is around ~48% which is not great.
- For some reason behat tests are failing in scrutinizee-ci, I'm investigating this.
- No proper exception handling.
- The correct HTTP codes are not always returned, you might get 500s instead of 400s.
- A data validation layer is lacking.
- No emphasis was put on security, as far as SQL injections, type casting and using PDO are the only implemented measures, your should do more in real apps.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
