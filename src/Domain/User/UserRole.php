<?php

namespace Scheduler\Domain\User;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    const MANAGER = 'manager';
    const EMPLOYEE = 'employee';
}
