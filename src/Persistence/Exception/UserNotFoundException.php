<?php

namespace Scheduler\Persistence\Exception;

use InvalidArgumentException;

class UserNotFoundException extends InvalidArgumentException
{
    protected $message = 'User not found';
}
