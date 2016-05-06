<?php

namespace Test\Domain\User\Action;

use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Equip\Payload;
use Mockery as m;
use Scheduler\Domain\User\Action\UserProvider;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Symfony\Component\Config\Definition\Exception\Exception;

class UserProviderTest extends \PHPUnit_Framework_TestCase
{

    public function test_returns_a_bad_request_payload_if_the_user_is_not_found()
    {
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andThrow(Exception::class);

        $userProvider = new UserProvider($userRepositoryMock, new Payload());
        /** @var PayloadInterface $payload */
        $payload = $userProvider([]);

        $this->assertEquals(Status::STATUS_BAD_REQUEST, $payload->getStatus());
    }

    public function test_returns_payload_with_a_valid_user()
    {
        $userMock = m::mock(User::class);
        $userRepositoryMock = m::mock(UserRepositoryInterface::class);
        $userRepositoryMock->shouldReceive('findOneById')->andReturn($userMock);

        $userProvider = new UserProvider($userRepositoryMock, new Payload());

        /** @var PayloadInterface $payload */
        $payload = $userProvider(['id' => 'ID']);

        $this->assertEquals(Status::STATUS_OK, $payload->getStatus());
    }
}
