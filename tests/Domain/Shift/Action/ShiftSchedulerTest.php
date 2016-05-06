<?php

namespace Test\Domain\Shift;

use DateTime;
use Equip\Adr\Status;
use Equip\Payload;
use Exception;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Scheduler\Domain\Shift\Action\ShiftScheduler;
use Scheduler\Domain\Shift\Contract\ShiftFactoryInterface;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Mockery as m;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Middleware\UserExtractor;

class ShiftSchedulerTest extends PHPUnit_Framework_TestCase
{
    public function test_returns_an_internal_server_status_payload_if_an_exception_is_thrown()
    {
        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $shiftFactoryInterface = m::mock(ShiftFactoryInterface::class);
        $payload = new Payload();

        $shiftProvider = new ShiftScheduler(
            $shiftRepositoryMock,
            $userRepositoryMock,
            $shiftFactoryInterface,
            $payload
        );

        $input = [
            UserExtractor::USER_ATTRIBUTE => 'ID'
        ];

        $shiftRepositoryMock->shouldReceive('findOneById')->andThrow(Exception::class);
        $payload = $shiftProvider($input);

        $this->assertEquals(Status::STATUS_INTERNAL_SERVER_ERROR, $payload->getStatus());
    }

    public function test_returns_a_bad_request_status_payload_if_an_exception_is_thrown()
    {
        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andThrow(InvalidArgumentException::class);

        $shiftFactoryInterface = m::mock(ShiftFactoryInterface::class);
        $payload = new Payload();

        $shiftScheduler = new ShiftScheduler(
            $shiftRepositoryMock,
            $userRepositoryMock,
            $shiftFactoryInterface,
            $payload
        );

        $input = [
            UserExtractor::USER_ATTRIBUTE => 'ID',
            'employee_id' => 'asd',
            'start_time' => 'today',
            'end_time' => 'tomorrow',
            'break' => '0',
        ];

        $payload = $shiftScheduler($input);

        $this->assertEquals(Status::STATUS_BAD_REQUEST, $payload->getStatus());
    }

    public function test_schedules_a_shift_and_returns_it_withing_the_payload_output()
    {
        $shiftMock = m::mock(Shift::class);
        $userMock = m::mock(User::class);

        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andReturn($userMock);


        $shiftFactoryInterface = m::mock(ShiftFactoryInterface::class);
        $shiftFactoryInterface->shouldReceive('fromInputData')->andReturn($shiftMock);

        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $shiftRepositoryMock->shouldReceive('saveShift')->with($userMock, $shiftMock);

        $payload = new Payload();

        $shiftScheduler = new ShiftScheduler(
            $shiftRepositoryMock,
            $userRepositoryMock,
            $shiftFactoryInterface,
            $payload
        );

        $input = [
            UserExtractor::USER_ATTRIBUTE => 'ID',
            'employee_id' => 'asd',
            'start_time' => 'today',
            'end_time' => 'tomorrow',
            'break' => '0',
        ];

        $payload = $shiftScheduler($input);

        $this->assertEquals(Status::STATUS_CREATED, $payload->getStatus());
        $this->assertEquals($shiftMock, $payload->getOutput()['shift']);
    }
}
