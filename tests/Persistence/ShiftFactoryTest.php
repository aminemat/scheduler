<?php

namespace Test\Persistence;

use App\Config\Config;
use Mockery as m;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Persistence\ShiftFactory;

class ShiftFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShiftFactory
     */
    private $shiftFactory;

    public function setUp()
    {
        $userMock = m::mock(User::class);
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andReturn(
            $userMock
        );

        $config = new Config();
        $config = $config->withData(
            [
                'DEFAULT_TIMEZONE' => 'UTC'
            ]
        );

        $this->shiftFactory = new ShiftFactory($userRepositoryMock, $config);
    }

    public function test_creates_a_valid_shift_fron_an_array_of_database_data()
    {
        $dbData = [
            'id' => 'id',
            'start_time' => '2016-05-18 16:00:00',
            'end_time' => '2016-05-18 20:00:00',
            'manager_id' => 'manager_1',
            'break' => 0.5,
            'employee_id' => 'employee_1',
        ];

        $expectedShift = new Shift(
            new EntityId($dbData['id']),
            new \DateTime($dbData['start_time']),
            new \DateTime($dbData['end_time']),
            m::mock(User::class),
            $dbData['break'],
            m::mock(User::class)
        );

        $actualShift = $this->shiftFactory->fromArray($dbData);

        $this->assertEquals($expectedShift, $actualShift);
    }

    public function test_creates_a_valid_shift_fron_an_array_of_input_data()
    {
        $inputData = [
            'manager' => m::mock(User::class),
            'start_time' => '2016-05-18 16:00:00',
            'end_time' => '2016-05-18 20:00:00',
            'manager_id' => 'manager_1',
            'break' => 0.5,
            'employee_id' => 'employee_1',
        ];

        $expectedShift = new Shift(
            new EntityId(),
            new \DateTime($inputData['start_time']),
            new \DateTime($inputData['end_time']),
            m::mock(User::class),
            $inputData['break'],
            m::mock(User::class)
        );

        $actualShift = $this->shiftFactory->fromArray($inputData);

        $this->assertInstanceOf(EntityId::class, $actualShift->getId());
        $this->assertEquals($expectedShift->getStartTime(), $actualShift->getStartTime());
        $this->assertEquals($expectedShift->getEndTime(), $actualShift->getEndTime());
        $this->assertEquals($expectedShift->getEmployee(), $actualShift->getEmployee());
        $this->assertEquals($expectedShift->getManager(), $actualShift->getManager());
        $this->assertEquals($expectedShift->getBreak(), $actualShift->getBreak());
    }
}
