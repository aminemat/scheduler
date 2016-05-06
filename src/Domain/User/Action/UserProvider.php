<?php

namespace Scheduler\Domain\User\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;

class UserProvider implements DomainInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var PayloadInterface
     */
    private $payload;

    /**
     * UserProvider constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param PayloadInterface        $payload
     */
    public function __construct(UserRepositoryInterface $userRepository, PayloadInterface $payload)
    {
        $this->userRepository = $userRepository;
        $this->payload = $payload;
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
        try {
            $user = $this->userRepository->findOneById((string) $input['id']);
        } catch (\Exception $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_BAD_REQUEST)
                ->withOutput([
                    'errors' => [
                        $exception->getMessage(),
                    ],
                ]);
        }

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput([
                'user' => $user,
            ]);
    }
}
