<?php

namespace Test\Persistence;

use Mockery as m;
use Scheduler\Domain\EntityId;
use Scheduler\Persistence\Exception\InvalidRoleException;
use Scheduler\Persistence\UserFactory;

class UserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    public function setUp()
    {
        $this->userFactory = new UserFactory();
    }

    public function test_throws_an_exception_when_an_an_invalid_role_is_supplied()
    {
        $this->expectException(InvalidRoleException::class);

        $this->userFactory->fromData([
            'role' => 'somerole'
        ]);

    }

    public function test_creates_a_valid_user_fron_an_array_of_data()
    {
        $inputData = [
            'id' => 'ID',
            'role' => 'employee',
            'name' => 'John Doe',
            'email' => 'foo@bar.com',
            'phone' => '(952) 1230 4569',
        ];

        $actualUser = $this->userFactory->fromData($inputData);

        $this->assertInstanceOf(EntityId::class, $actualUser->getId());
        $this->assertEquals($inputData['id'], $actualUser->getId());
        $this->assertEquals($inputData['role'], $actualUser->getRole());
        $this->assertEquals($inputData['name'], $actualUser->getName());
        $this->assertEquals($inputData['email'], $actualUser->getEmail());
        $this->assertEquals($inputData['phone'], $actualUser->getPhone());
    }
}
