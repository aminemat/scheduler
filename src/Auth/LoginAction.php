<?php

namespace Scheduler\Auth;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Equip\Auth\AuthHandler;
use Equip\Auth\Token;

class LoginAction implements DomainInterface
{
    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * Login constructor.
     *
     * @param PayloadInterface $payloadInterface
     */
    public function __construct(PayloadInterface $payloadInterface)
    {
        $this->payload = $payloadInterface;
    }

    /**
     * Handle domain logic for an action.
     *
     * @param array $input
     *
     * @return PayloadInterface
     */
    public function __invoke(array $input)
    {
        /* @var Token $accessToken */
        $token = $input[AuthHandler::TOKEN_ATTRIBUTE];

        $accessToken = $token->getToken();
        $metadata = $token->getMetadata();

        return $this->payload->withStatus(Status::STATUS_OK)
            ->withOutput([
                'token' => $accessToken,
                'user' => [
                    'id' => $metadata['userId'],
                    'name' => $metadata['userName'],
                    'role' => $metadata['userRole'],
                    'email' => $metadata['userEmail'],
                ],
            ]);
    }
}
