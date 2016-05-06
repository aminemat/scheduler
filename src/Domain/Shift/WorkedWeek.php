<?php

namespace Scheduler\Domain\Shift;

class WorkedWeek
{
    /**
     * @var string
     */
    private $weekNumber;

    /**
     * @var float
     */
    private $workedHours;

    /**
     * WorkedWeek constructor.
     *
     * @param string $weekNumber
     * @param float  $workedHours
     */
    public function __construct($weekNumber, $workedHours)
    {
        $this->weekNumber = $weekNumber;
        $this->workedHours = $workedHours;
    }

    /**
     * @return string format yyyyW, example 201621: year 2016, week number 21
     */
    public function getWeekNumber()
    {
        return $this->weekNumber;
    }

    /**
     * @return int, in seconds
     */
    public function getWorkedHours()
    {
        return $this->workedHours;
    }
}
