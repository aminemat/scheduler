<?php

namespace Scheduler\Persistence;

use DateTime;
use Doctrine\DBAL\Connection;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\Shift\WeeklySummary;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Persistence\Exception\EmployeeNotAvailableException;
use Scheduler\Persistence\Exception\ShiftNotFoundException;

class DBALShiftRepository implements ShiftRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ShiftFactory
     */
    private $shiftFactory;
    /**
     * @var WeeklySummaryFactory
     */
    private $weeklySummaryFactory;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * PDOUserRepository constructor.
     *
     * @param Connection              $connection
     * @param ShiftFactory            $shiftFactory
     * @param WeeklySummaryFactory    $weeklySummaryFactory
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        Connection $connection,
        ShiftFactory $shiftFactory,
        WeeklySummaryFactory $weeklySummaryFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->connection = $connection;
        $this->shiftFactory = $shiftFactory;
        $this->weeklySummaryFactory = $weeklySummaryFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User          $employee
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param bool          $coworkers
     *
     * @return \Scheduler\Domain\Shift\Shift[]
     */
    public function fetchAllByEmployee(
        User $employee,
        DateTime $startDate,
        DateTime $endDate,
        $coworkers = false
    ) {
        $stmt = $this->connection->createQueryBuilder()
            ->select('s.*')
            ->from('shift', 's')
            ->where('start_time >= :startTime')
            ->andWhere('end_time <= :endTime')
            ->setParameter('startTime', $startDate, 'datetimetz')
            ->setParameter('endTime', $endDate, 'datetimetz')
            ->orderBy('start_time');

        $employeeOnly = 'employee_id = :employeeId';
        $coworkersOnly = 'employee_id != :employeeId';

        $where = $coworkers ? $coworkersOnly : $employeeOnly;
        $stmt->andWhere($where)->setParameter('employeeId', (string) $employee->getId());

        $result = $stmt->execute();
        $rows = $result->fetchAll();

        $shifts = [];
        foreach ($rows as $row) {
            $shifts[] = $this->shiftFactory->fromDBData($row);
        }

        return $shifts;
    }

    /**
     * @param DateTime|null $startDate
     * @param DateTime|null $endDate
     * @param bool          $coworkers
     *
     * @return \Scheduler\Domain\Shift\Shift[]
     */
    public function fetchAll(
        DateTime $startDate,
        DateTime $endDate,
        $coworkers = false
    ) {
        $stmt = $this->connection->createQueryBuilder()
            ->select('s.*')
            ->from('shift', 's')
            ->where('start_time >= :startTime')
            ->andWhere('end_time <= :endTime')
            ->setParameter('startTime', $startDate, 'datetimetz')
            ->setParameter('endTime', $endDate, 'datetimetz')
            ->orderBy('start_time');

        $result = $stmt->execute();
        $rows = $result->fetchAll();

        $shifts = [];
        foreach ($rows as $row) {
            $shifts[] = $this->shiftFactory->fromDBData($row);
        }

        return $shifts;
    }

    /**
     * @param User $employee
     *
     * @return WeeklySummary
     */
    public function getWeeklySummary(User $employee)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('YEARWEEK(`end_time`) as name, SUM(TIME_TO_SEC(TIMEDIFF(`end_time`, `start_time`))) as hours')
            ->from('shift', 's')
            ->where('employee_id = :employeeId')
            ->groupBy('YEARWEEK(`end_time`)')
            ->setParameter('employeeId', (string) $employee->getId())
            ->execute();

        return $this->weeklySummaryFactory->fromData($employee, $stmt->fetchAll());
    }

    /**
     * Persists a Shift entity.
     *
     * @param User  $manager
     * @param Shift $shift
     *
     * @return Shift
     *
     * @throws EmployeeNotAvailableException
     */
    public function saveShift(User $manager, Shift $shift)
    {
        if (!$this->userRepository->isAvailable(
            $shift->getEmployee(),
            $shift->getStartTime(),
            $shift->getEndTime()
        )
        ) {
            throw new EmployeeNotAvailableException();
        }

        $this->connection->createQueryBuilder()
            ->insert('shift')
            ->values([
                'id' => ':id',
                'manager_id' => ':managerId',
                'employee_id' => ':employeeId',
                'break' => ':break',
                'start_time' => ':startTime',
                'end_time' => ':endTime',
            ])
            ->setParameters([
                'id' => (string) $shift->getId(),
                'managerId' => (string) $manager->getId(),
                'employeeId' => (string) $shift->getEmployee()->getId(),
                'break' => $shift->getBreak(),
            ])
            ->setParameter('startTime', $shift->getStartTime(), 'datetimetz')
            ->setParameter('endTime', $shift->getEndTime(), 'datetimetz')
            ->execute();
    }

    /**
     * Updates am existing Shift entity.
     *
     * @param User  $manager
     * @param Shift $shift
     *
     * @return Shift
     *
     * @throws EmployeeNotAvailableException
     */
    public function updateShift(User $manager, Shift $shift)
    {
        if (!$this->userRepository->isAvailable(
            $shift->getEmployee(),
            $shift->getStartTime(),
            $shift->getEndTime()
        )
        ) {
            throw new EmployeeNotAvailableException();
        }

        $this->connection->createQueryBuilder()
            ->update('shift')
            ->set('employee_id', ':employeeId')
            ->set('start_time', ':startTime')
            ->set('end_time', ':endTime')
            ->where('id = :id')
            ->setParameters([
                'id' => (string) $shift->getId(),
                'employeeId' => (string) $shift->getEmployee()->getId(),
            ])
            ->setParameter('startTime', $shift->getStartTime(), 'datetimetz')
            ->setParameter('endTime', $shift->getEndTime(), 'datetimetz')
            ->execute();
    }

    /**
     * @param string $shiftId
     *
     * @return Shift
     *
     * @throws ShiftNotFoundException
     */
    public function findOneById($shiftId)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('shift', 's')
            ->where('id = :id')
            ->setParameter('id', $shiftId)
            ->execute();

        if (!$row = $stmt->fetch()) {
            throw new ShiftNotFoundException();
        }

        return $this->shiftFactory->fromDBData($row);
    }
}
