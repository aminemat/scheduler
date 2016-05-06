<?php

namespace Scheduler\Persistence;

use Doctrine\DBAL\Connection;
use Equip\Auth\Credentials;
use Equip\Auth\Exception\InvalidException;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Persistence\Exception\UserNotFoundException;

class DBALUserRepository implements UserRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * PDOUserRepository constructor.
     *
     * @param Connection  $connection
     * @param UserFactory $userFactory
     */
    public function __construct(Connection $connection, UserFactory $userFactory)
    {
        $this->connection = $connection;
        $this->userFactory = $userFactory;
    }

    /**
     * @param Credentials $credentials
     *
     * @return User
     *
     * @throws Exception\InvalidRoleException
     * @throws InvalidException
     * @throws UserNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function loadUserByCredentials(Credentials $credentials)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user', 'u')
            ->where('email = :email')
            ->setParameter('email', $credentials->getIdentifier())
            ->execute();

        if (!$row = $stmt->fetch()) {
            throw new UserNotFoundException();
        }

        if (!password_verify($credentials->getPassword(), $row['password'])) {
            throw InvalidException::incorrectPassword($credentials->getIdentifier());
        }

        return $this->userFactory->fromData($row);
    }

    /**
     * @param string $userId
     *
     * @return User
     *
     * @throws UserNotFoundException
     */
    public function findOneById($userId)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user', 'u')
            ->where('id = :id')
            ->setParameter('id', $userId)
            ->execute();

        if (!$row = $stmt->fetch()) {
            throw new UserNotFoundException();
        }

        return $this->userFactory->fromData($row);
    }

    /**
     * Returns true if the employee is available between a start and end dates.
     *
     * @param User      $employee
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return bool|string
     */
    public function isAvailable(User $employee, \DateTime $startDate, \DateTime $endDate)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('count(id)')
            ->from('shift', 's')
            ->where('employee_id = :employeeId')
            ->andWhere('start_time >= :startTime')
            ->andWhere('end_time <= :endTime')
            ->setParameter('startTime', $startDate, 'datetime')
            ->setParameter('endTime', $endDate, 'datetime')
            ->setParameter('employeeId', (string) $employee->getId())
            ->execute();

        return $stmt->fetchColumn() > 0 ? false : true;
    }
}
