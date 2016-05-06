<?php

namespace Test\Domain\Shift;

use DateTime;
use \InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\Shift\Shift;
use Mockery as m;
use Scheduler\Domain\User\User;

class ShiftTest extends PHPUnit_Framework_TestCase
{
    public function test_throws_an_exception_when_startDate_precedes_endDate()
    {
        $this->expectException(InvalidArgumentException::class);
        new Shift(
            new EntityId('ID'),
            new DateTime('now + 1 hour'),
            new DateTime('now'),
            m::mock(User::class)
        );
    }
}
