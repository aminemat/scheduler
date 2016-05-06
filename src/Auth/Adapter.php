<?php

namespace Scheduler\Auth;

use Equip\Auth\AdapterInterface;
use Equip\Auth\Credentials;
use Equip\Auth\Exception\InvalidException;
use Equip\Auth\Jwt\GeneratorInterface;
use Equip\Auth\Jwt\ParserInterface;
use Equip\Auth\Token;
use Scheduler\Domain\User\Contract\TokenRepositoryInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;

class Adapter implements AdapterInterface
{
    /**
     * @var GeneratorInterface
     */
    private $generator;

    /**
     * @var ParserInterface
     */
    private $parser;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var TokenRepositoryInterface
     */
    private $tokenRepository;

    /**
     * Adapter constructor.
     *
     * @param GeneratorInterface       $generator
     * @param ParserInterface          $parser
     * @param UserRepositoryInterface  $userRepository
     * @param TokenRepositoryInterface $tokenRepository
     */
    public function __construct(
        GeneratorInterface $generator,
        ParserInterface $parser,
        UserRepositoryInterface $userRepository,
        TokenRepositoryInterface $tokenRepository
    ) {
        $this->generator = $generator;
        $this->parser = $parser;
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Validates a specified authentication token.
     *
     * - If the specified token is invalid, an InvalidException instance is
     *   thrown.
     * - If a valid token is present, a corresponding Token instance is
     *   returned.
     * - If for some reason the token cannot be validated, an AuthException
     *   instance is thrown.
     *
     * @param string $token
     *
     * @return \Equip\Auth\Token
     *
     * @throws \Equip\Auth\Exception\InvalidException if an invalid auth token
     *                                                is specified
     * @throws \Equip\Auth\Exception\AuthException    if another error occurs
     *                                                during authentication
     */
    public function validateToken($token)
    {
        if (!$this->tokenRepository->tokenExists($token)) {
            throw InvalidException::invalidToken($token);
        }

        $parsed = $this->parser->parseToken((string) $token);

        return $parsed;
    }

    /**
     * Validates a set of user credentials.
     *
     * - If the user credentials are valid, a new authentication token is
     *   created and a corresponding Token instance is returned.
     * - If the user credentials are invalid, an InvalidException instance is
     *   thrown.
     * - If for some reason the user credentials cannot be validated, an
     *   AuthException instance is thrown.
     *
     * @param \Equip\Auth\Credentials $credentials
     *
     * @return \Equip\Auth\Token
     *
     * @throws \Equip\Auth\Exception\InvalidException if an invalid auth token
     *                                                is specified
     * @throws \Equip\Auth\Exception\AuthException    if another error occurs
     *                                                during authentication
     */
    public function validateCredentials(Credentials $credentials)
    {
        try {
            $user = $this->userRepository->loadUserByCredentials($credentials);
        } catch (\Exception $e) {
            throw new InvalidException();
        }

        $metadata = [
            'userId' => (string) $user->getId(),
            'userRole' => (string) $user->getRole(),
            'userName' => (string) $user->getName(),
            'userEmail' => (string) $user->getEmail(),
        ];

        $claims = [
            'jti' => 'tokenId',
            'data' => [
                $metadata,
            ],
        ];

        $token = $this->generator->getToken($claims);
        $this->tokenRepository->saveToken($token, $user->getId());

        return new Token($token, $metadata);
    }
}
