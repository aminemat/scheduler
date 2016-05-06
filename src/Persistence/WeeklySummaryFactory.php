<?php

namespace Scheduler\Persistence;

use Scheduler\Domain\Shift\WeeklySummary;
use Scheduler\Domain\Shift\WorkedWeek;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;

class WeeklySummaryFactory
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * ShiftFactory constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param User  $employee
     * @param array $summary
     *
     * @return WeeklySummary
     */
    public function fromData(User $employee, $summary)
    {
        $weeklySummary = new WeeklySummary($employee);
        foreach ($summary as $week) {
            $weeklySummary->addWorkedWeek(
                new WorkedWeek($week['name'], $week['hours'])
            );
        }

        return $weeklySummary;
    }
}
