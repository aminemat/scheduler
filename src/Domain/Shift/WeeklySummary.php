<?php

namespace Scheduler\Domain\Shift;

use Scheduler\Domain\User\User;

class WeeklySummary
{
    /**
     * @var User
     */
    private $employee;

    /**
     * @var WorkedWeek[]
     */
    private $workedWeeks;

    /**
     * WeeklySummary constructor.
     *
     * @param User         $employee
     * @param WorkedWeek[] $workedWeeks
     */
    public function __construct(User $employee, array $workedWeeks = [])
    {
        $this->employee = $employee;
        $this->workedWeeks = $workedWeeks;
    }

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @return WorkedWeek[]
     */
    public function getWorkedWeeks()
    {
        return $this->workedWeeks;
    }

    public function addWorkedWeek(WorkedWeek $workedWeek)
    {
        $this->workedWeeks[] = $workedWeek;
    }
}
