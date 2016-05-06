<?php

namespace Scheduler\Domain\Shift\Contract;

use Scheduler\Domain\Shift\Shift;

interface ShiftFactoryInterface
{
    /**
     * Constructs a Shift entity from an array of data.
     *
     * @param array $data
     *
     * required keys:
     * [
     *   'id' => string,
     *   'start_time' => string,
     *   'start_time' => string,
     *   'manager_id' => string,
     *   'employee_id' => string,
     *   'break' => float|string,
     * ]
     *
     * @return Shift
     */
    public function fromDBData(array $data);

    /**
     * Constructs a Shift entity from an array of data.
     *
     * @param array $data
     *
     * required keys:
     * [
     *   'manager' => User,
     *   'start_time' => string,
     *   'start_time' => string,
     *   'manager_id' => string,
     *   'employee_id' => string,
     *   'break' => float|string,
     * ]
     *
     * @return Shift
     */
    public function fromInputData(array $data);
}
