<?php

namespace Scheduler\Domain\Shift;

use DateTime;
use InvalidArgumentException;
use Respect\Validation\Rules\Date;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\TimestampableEntity;
use Scheduler\Domain\User\User;

class Shift
{
    use TimestampableEntity;

    /**
     * @var EntityId
     */
    private $id;

    /**
     * @var User
     */
    private $manager;

    /**
     * @var User
     */
    private $employee;

    /**
     * @var float
     */
    private $break;

    /**
     * @var DateTime
     */
    private $startTime;

    /**
     * @var DateTime
     */
    private $endTime;

    /**
     * Shift constructor.
     *
     * @param EntityId $id
     * @param DateTime $startTime
     * @param DateTime $endTime
     * @param User     $manager
     * @param float    $break
     * @param User     $employee
     */
    public function __construct(
        EntityId $id,
        DateTime $startTime,
        DateTime $endTime,
        User $manager,
        $break = null,
        User $employee = null
    ) {
        if ($endTime < $startTime) {
            throw new InvalidArgumentException('End date precedes start date');
        }

        if (!$this->getCreatedAt()) {
            $this->createdAt = new DateTime();
        }

        $this->id = $id;
        $this->break = $break;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->manager = $manager;
        $this->employee = $employee;
    }

    /**
     * @return EntityId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @return float
     */
    public function getBreak()
    {
        return $this->break;
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param User $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }

    /**
     * @param DateTime $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @param DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @param float $break
     */
    public function setBreak($break)
    {
        $this->break = $break;
    }
}
