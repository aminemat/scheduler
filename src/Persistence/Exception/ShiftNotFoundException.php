<?php

namespace Scheduler\Persistence\Exception;

use InvalidArgumentException;

class ShiftNotFoundException extends InvalidArgumentException
{
    protected $message = 'Shift not found';
}
