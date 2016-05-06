<?php

namespace Test\Persistence;

use Mockery as m;
use Scheduler\Domain\Shift\WeeklySummary;
use Scheduler\Domain\Shift\WorkedWeek;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Persistence\WeeklySummaryFactory;

class WeeklySummaryFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var WeeklySummaryFactory
     */
    private $weeklySummaryFactory;

    public function setUp()
    {
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $this->weeklySummaryFactory = new WeeklySummaryFactory($userRepositoryMock);
    }

    public function test_creates_a_weekly_summary_from_an_array_of_data()
    {
        $userMock = m::mock(User::class);
        $data = [
            [
                'name' => '52',
                'hours' => 30
            ]
        ];
        $actualSummary = $this->weeklySummaryFactory->fromData($userMock, $data);

        $this->assertCount(1, $actualSummary->getWorkedWeeks());
        $this->assertInstanceOf(WorkedWeek::class, $actualSummary->getWorkedWeeks()[0]);
        $this->assertEquals('52', $actualSummary->getWorkedWeeks()[0]->getWeekNumber());
        $this->assertEquals(30, $actualSummary->getWorkedWeeks()[0]->getWorkedHours());
    }
}
