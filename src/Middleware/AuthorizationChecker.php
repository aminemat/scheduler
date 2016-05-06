<?php

namespace Scheduler\Middleware;

use Equip\Auth\Exception\UnauthorizedException;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\UserRole;

/**
 * Class AuthorizationChecker.
 *
 * Poor man Authorization middleware:
 * - Denies All unauthenticated requests to all endpoints except login
 * - Uses the HTTP verb to authorize users by checking their roles
 *    ROLE_MANGER: GET, POST, PUT, DELETE
 *    ROLE_EMPLOYEE: GET
 */
class AuthorizationChecker
{
    /**
     * Login URL.
     */
    const LOGIN_PATH = '/v1/login';

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * AuthorizationChecker constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $isLoginRequest = self::LOGIN_PATH === $request->getUri()->getPath();
        if ($isLoginRequest) {
            return $next($request, $response);
        }

        //All endpoints except login need authentication
        $userId = $request->getAttribute(UserExtractor::USER_ATTRIBUTE);
        if (!$isLoginRequest && !$userId) {
            throw UnauthorizedException::noToken();
        }

        try {
            $user = $this->userRepository->findOneById($userId);
            if ($user->getRole() == UserRole::EMPLOYEE && 'GET' !== $request->getMethod()) {
                throw UnauthorizedException::noToken();
            }
        } catch (Exception $e) {
            throw UnauthorizedException::noToken();
        }

        return $next($request, $response);
    }
}
