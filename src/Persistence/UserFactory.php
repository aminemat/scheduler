<?php

namespace Scheduler\Persistence;

use Scheduler\Domain\EntityId;
use Scheduler\Domain\User\User;
use Scheduler\Domain\User\UserRole;
use Scheduler\Persistence\Exception\InvalidRoleException;

class UserFactory
{
    /**
     * @param array $data
     *
     * @return User
     *
     * @throws InvalidRoleException
     */
    public function fromData(array $data)
    {
        $roleName = strtoupper($data['role']);
        if (!UserRole::isValid($data['role'])) {
            throw new InvalidRoleException();
        }

        return new User(
            new EntityId($data['id']),
            UserRole::$roleName(),
            $data['name'],
            $data['email'],
            $data['phone']
        );
    }
}
