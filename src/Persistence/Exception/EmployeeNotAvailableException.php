<?php

namespace Scheduler\Persistence\Exception;

use InvalidArgumentException;

class EmployeeNotAvailableException extends InvalidArgumentException
{
    protected $message = 'Employee is not available during this time frame';
}
