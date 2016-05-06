<?php

namespace Test\Domain\User;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\User\User;
use Scheduler\Domain\User\UserRole;
use UnexpectedValueException;


class UserTest extends PHPUnit_Framework_TestCase
{

    public function test_throws_an_exception_when_creating_a_user_with_an_invalid_role()
    {
        $this->expectException(UnexpectedValueException::class);
        new User(new EntityId('fakeid'), new UserRole('foo'), 'username');
    }

    public function test_throws_an_exception_when_email_and_phone_are_empty()
    {
        $this->expectException(InvalidArgumentException::class);
        new User(new EntityId('fakeid'), UserRole::EMPLOYEE(), 'username');
    }

    public function test_creates_a_valid_manager()
    {
        $userId = new EntityId('fakeid');
        $user = new User($userId, UserRole::MANAGER(), 'username', 'foo@bar.com');
        
        $this->assertEquals($userId, $user->getId());
        $this->assertEquals(UserRole::MANAGER(), $user->getRole());
        $this->assertEquals('username', $user->getName());
        $this->assertEquals('foo@bar.com', $user->getEmail());
        $this->assertNull($user->getPhone());
    }

    public function test_creates_a_valid_employee()
    {
        $userId = new EntityId('fakeid');
        $user = new User($userId, UserRole::EMPLOYEE(), 'username', null, '+123456789');
        
        $this->assertEquals($userId, $user->getId());
        $this->assertEquals(UserRole::EMPLOYEE(), $user->getRole());
        $this->assertEquals('username', $user->getName());
        $this->assertNull($user->getEmail());
        $this->assertEquals('+123456789', $user->getPhone());
    }
}
