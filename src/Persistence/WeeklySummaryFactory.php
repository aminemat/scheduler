<?php

namespace Scheduler\Persistence;

use Scheduler\Domain\Shift\WeeklySummary;
use Scheduler\Domain\Shift\WorkedWeek;
use Scheduler\Domain\User\User;

class WeeklySummaryFactory
{
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
