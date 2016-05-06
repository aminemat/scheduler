<?php

namespace Test\Domain\Shift;

use DateTime;
use Equip\Adr\Status;
use Equip\Payload;
use Exception;
use PHPUnit_Framework_TestCase;
use Scheduler\Domain\Shift\Action\ShiftProvider;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Mockery as m;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Middleware\UserExtractor;

class ShiftProviderTest extends PHPUnit_Framework_TestCase
{

    public function test_returns_an_internal_server_payload_if_an_exception_is_thrown()
    {
        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $payloadMock = new Payload();

        $shiftProvider = new ShiftProvider($shiftRepositoryMock, $userRepositoryMock, $payloadMock);
        $input = [];
        $shiftRepositoryMock->shouldReceive('findOneById')->andThrow(Exception::class);
        $payload = $shiftProvider($input);

        $this->assertEquals(Status::STATUS_INTERNAL_SERVER_ERROR, $payload->getStatus());
    }

    public function test_returns_all_shifts_if_the_requester_is_a_manager()
    {
        $userMock = m::mock(User::class);
        $userMock->shouldReceive('isManager')->andReturn(true);

        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andReturn($userMock);

        $shifts = ['shift_1', 'shift_2', 'shift_3'];

        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $shiftRepositoryMock->shouldReceive('fetchAll')->andReturn($shifts);

        $payloadMock = new Payload();

        $shiftProvider = new ShiftProvider($shiftRepositoryMock, $userRepositoryMock, $payloadMock);
        $input = [
            UserExtractor::USER_ATTRIBUTE => 'ID'
        ];

        $payload = $shiftProvider($input);

        $this->assertEquals(Status::STATUS_OK, $payload->getStatus());
        $this->assertEquals($shifts, $payload->getOutput()['shifts']);
    }

    public function test_restricts_shifts_by_employee_if_the_requester_is_an_employee()
    {
        $userMock = m::mock(User::class);
        $userMock->shouldReceive('isManager')->andReturn(false);

        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andReturn($userMock);

        $shifts = ['shift_1', 'shift_2', 'shift_3'];

        $shiftRepositoryMock = m::mock(ShiftRepositoryInterface::class);
        $shiftRepositoryMock->shouldReceive('fetchAllByEmployee')->andReturn($shifts);

        $payloadMock = new Payload();

        $shiftProvider = new ShiftProvider($shiftRepositoryMock, $userRepositoryMock, $payloadMock);
        $input = [
            UserExtractor::USER_ATTRIBUTE => 'ID'
        ];

        $payload = $shiftProvider($input);

        $this->assertEquals(Status::STATUS_OK, $payload->getStatus());
        $this->assertEquals($shifts, $payload->getOutput()['shifts']);
    }
}
