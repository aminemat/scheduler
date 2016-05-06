<?php

namespace Test\Auth;

use Equip\Adr\Status;
use Equip\Auth\Token;
use Equip\Payload;
use Mockery as m;
use Scheduler\Auth\LoginAction;

class LoginActionTests extends \PHPUnit_Framework_TestCase
{

    public function testInvoke()
    {
        $loginAction = new LoginAction(new Payload());
        $token = 'someToken';
        $userId = 'ID';
        $userRole = 'employee';
        $userEmail = 'foo@bar.com';
        $userName = 'foobar';

        $metadata = [
            'userId' => $userId,
            'userRole' => $userRole,
            'userName' => $userName,
            'userEmail' => $userEmail
        ];

        $authToken = new Token($token, $metadata);

        $input = [
            'spark/auth:token' => $authToken
        ];

        $actualPayload = $loginAction($input);

        $expectedPayload = (new Payload())
            ->withStatus(Status::STATUS_OK)
            ->withOutput([
                'token' => $token,
                'user' => [
                    'id' => $userId,
                    'name' => $userName,
                    'role' => $userRole,
                    'email' => $userEmail
                ]
            ]);

        $this->assertEquals($expectedPayload, $actualPayload);
    }
}
