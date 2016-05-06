<?php

namespace Scheduler\Middleware;

use Equip\Auth\AuthHandler;
use Equip\Auth\Token;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class UserExtractor - middleware.
 *
 * Extracts a User entity from a Token object and stores it
 * in the request for easy access.
 */
class UserExtractor
{
    /**
     * Namespace for the request userId attribute.
     */
    const USER_ATTRIBUTE = 'scheduler/user';

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        /** @var Token $token */
        $token = $request->getAttribute(AuthHandler::TOKEN_ATTRIBUTE);
        $metadata = $token->getMetadata();

        if (array_key_exists('data', $metadata) && !empty($metadata['data'][0]->userId)) {
            $request = $this->storeUserInRequest($request, $token->getMetadata()['data'][0]->userId);
        }

        return $next($request, $response);
    }

    /**
     * Extract the User and put it in the request.
     *
     * @param ServerRequestInterface $request
     * @param string                 $userId
     *
     * @return ServerRequestInterface
     */
    private function storeUserInRequest($request, $userId)
    {
        return $request->withAttribute(self::USER_ATTRIBUTE, $userId);
    }
}
