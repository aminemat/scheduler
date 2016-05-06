<?php

namespace Scheduler\Domain\Shift\Contract;

use DateTime;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\Shift\WorkedWeek;
use Scheduler\Domain\User\User;

interface ShiftRepositoryInterface
{
    /**
     * @param User          $employee
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param bool          $include_coworkers
     *
     * @return Shift[]
     */
    public function fetchAllByEmployee(
        User $employee,
        DateTime $startDate,
        DateTime $endDate,
        $include_coworkers = false
    );

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param bool          $include_coworkers
     *
     * @return Shift[]
     */
    public function fetchAll(
        DateTime $startDate,
        DateTime $endDate,
        $include_coworkers = false
    );

    /**
     * @param User $employee
     *
     * @return WorkedWeek
     */
    public function getWeeklySummary(User $employee);

    /**
     * Persists a Shift entity.
     *
     * @param User  $manager
     * @param Shift $shift
     *
     * @return
     */
    public function saveShift(User $manager, Shift $shift);

    /**
     * Updates an existing shift.
     *
     * @param User  $manager
     * @param Shift $shift
     *
     * @return
     */
    public function updateShift(User $manager, Shift $shift);

    /**
     * @param string $shiftId
     *
     * @return Shift
     */
    public function findOneById($shiftId);
}
