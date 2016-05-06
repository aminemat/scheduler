<?php

namespace Test\Auth;

use Equip\Auth\Credentials;
use Equip\Auth\Exception\InvalidException;
use Equip\Auth\Jwt\GeneratorInterface;
use Equip\Auth\Jwt\ParserInterface;
use Equip\Auth\Token;
use Mockery as m;
use Scheduler\Auth\Adapter;
use Scheduler\Domain\User\Contract\TokenRepositoryInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Adapter
     */
    private $adapter;
    private $userRepository;
    private $tokenRepository;
    private $parser;
    private $generator;

    public function setup()
    {
        $this->generator = m::mock(GeneratorInterface::class);
        $this->parser = m::mock(ParserInterface::class);
        $this->userRepository = $userRepository = m::mock(UserRepositoryInterface::class);
        $this->tokenRepository = m::mock(TokenRepositoryInterface::class);

        $this->adapter = new Adapter(
            $this->generator,
            $this->parser,
            $this->userRepository,
            $this->tokenRepository
        );
    }

    public function test_validates_token()
    {
        $token = 'non parsed token';
        $parsedToken = 'parsed token';

        $this->tokenRepository->shouldReceive('tokenExists')
            ->once()
            ->withArgs([
                $token
            ])
            ->andReturn(true);

        $this->parser->shouldReceive('parseToken')
            ->once()
            ->withArgs([
                $token
            ])
            ->andReturn($parsedToken);

        $this->assertEquals($parsedToken, $this->adapter->validateToken($token));
    }

    public function test_validates_credentials()
    {
        $token = 'foobar';
        $credentials = new Credentials('foo', 'bar');
        $userId = 'ID';
        $userRole = 'employee';
        $userEmail = 'foo@bar.com';
        $userName = 'foobar';

        $userMock = m::mock(User::class);
        $userMock->shouldReceive(
            [
                'getId' => $userId,
                'getRole' => $userRole,
                'getEmail' => $userEmail,
                'getName' => $userName
            ]
        );

        $this->userRepository->shouldReceive('loadUserByCredentials')
            ->once()
            ->withArgs([$credentials])
            ->andReturn($userMock);

        $this->generator->shouldReceive('getToken')
            ->once()
            ->andReturn($token);

        $this->tokenRepository->shouldReceive('saveToken')
            ->once();

        $expectedToken = new Token($token, [
            'userId' => $userId,
            'userRole' => $userRole,
            'userName' => $userName,
            'userEmail' => $userEmail
        ]);
        $actualToken = $this->adapter->validateCredentials($credentials);

        $this->assertEquals($expectedToken, $actualToken);
    }

    public function test_throws_an_invalid_exception_if_credentials_are_invalid()
    {
        $this->expectException(InvalidException::class);

        $this->userRepository->shouldReceive('loadUserByCredentials')->andThrow(\Exception::class);
        $this->adapter->validateCredentials(new Credentials('foo', 'bar'));
    }

    public function test_throws_an_invalid_exception_if_token_is_invalid()
    {
        $this->expectException(InvalidException::class);

        $this->tokenRepository->shouldReceive('tokenExists')->andReturn(false);
        $this->adapter->validateToken('asdasdasd');
    }
}
