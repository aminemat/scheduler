<?php

namespace Scheduler\Domain\User\Contract;

use Equip\Auth\Credentials;
use Scheduler\Domain\User\User;

interface UserRepositoryInterface
{
    /**
     * @param Credentials $credentials
     *
     * @return User
     */
    public function loadUserByCredentials(Credentials $credentials);

    /**
     * @param string $userId
     *
     * @return User
     */
    public function findOneById($userId);

    /**
     * @param User      $employee
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return bool
     */
    public function isAvailable(User $employee, \DateTime $startDate, \DateTime $endDate);
}
