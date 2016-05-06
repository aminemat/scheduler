<?php

namespace Scheduler\Transformer;

use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Scheduler\Domain\Shift\Shift;

class ShiftTransformer
{
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * ShiftTransformer constructor.
     *
     * @param UserTransformer $userTransformer
     */
    public function __construct(UserTransformer $userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }

    /**
     * @param Shift[] $shifts
     *
     * @return array
     */
    public function transformCollection($shifts)
    {
        $fractal = new Manager();
        $resource = new Collection($shifts, function (Shift $shift) {
            return $this->transform($shift);
        });

        return $fractal->createData($resource)->toArray();
    }

    /**
     * @param Shift $shift
     *
     * @return array
     */
    public function transform(Shift $shift)
    {
        return [
            'shift' => [
                'id' => (string) $shift->getId(),
                'start' => Carbon::createFromTimestamp($shift->getStartTime()->getTimestamp())->toRfc822String(),
                'end' => Carbon::createFromTimestamp($shift->getEndTime()->getTimestamp())->toRfc822String(),
                'break' => $shift->getBreak(),
                'manager' => $this->userTransformer->transform($shift->getManager()),
                'employee' => $this->userTransformer->transform($shift->getEmployee()),
            ],
        ];
    }
}
