<?php

namespace Scheduler\Domain\User;

use Carbon\Carbon;
use InvalidArgumentException;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\TimestampableEntity;

class User
{
    use TimestampableEntity;

    /**
     * @var EntityId
     */
    private $id;

    /**
     * @var UserRole
     */
    private $role;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * User constructor.
     *
     * @param EntityId $id
     * @param UserRole $role
     * @param string   $name
     * @param string   $phone
     * @param string   $email
     */
    public function __construct(EntityId $id, UserRole $role, $name, $email = null, $phone = null)
    {
        if (empty($phone) && empty($email)) {
            throw new InvalidArgumentException('Please provide an email address or a phone number');
        }

        if (!$this->getCreatedAt()) {
            $this->createdAt = Carbon::now()->toRfc822String();
        }

        $this->id = $id;
        $this->role = $role;
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * @return EntityId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return UserRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isManager()
    {
        return $this->role == UserRole::MANAGER();
    }

    /**
     * @return bool
     */
    public function isEmployee()
    {
        return $this->role == UserRole::EMPLOYEE();
    }
}
