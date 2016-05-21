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
     *   'manager_id' | 'manager' => string | User,
     *   'employee_id' => string,
     *   'break' => float|string,
     * ]
     *
     * @return Shift
     */
    public function fromArray(array $data);
}
