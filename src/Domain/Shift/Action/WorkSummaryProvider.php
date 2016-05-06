<?php

namespace Scheduler\Domain\Shift\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Middleware\UserExtractor;

class WorkSummaryProvider implements DomainInterface
{
    private $payload;

    /**
     * @var ShiftRepositoryInterface
     */
    private $shiftRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * ShiftProvider constructor.
     *
     * @param ShiftRepositoryInterface $shiftRepository
     * @param UserRepositoryInterface  $userRepository
     * @param PayloadInterface         $payload
     */
    public function __construct(
        ShiftRepositoryInterface $shiftRepository,
        UserRepositoryInterface $userRepository,
        PayloadInterface $payload
    ) {
        $this->payload = $payload;
        $this->shiftRepository = $shiftRepository;
        $this->userRepository = $userRepository;
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
        $employee = $this->userRepository->findOneById($input[UserExtractor::USER_ATTRIBUTE]);
        $summary = $this->shiftRepository->getWeeklySummary($employee);

        return $this->payload->withStatus(Status::STATUS_OK)
            ->withOutput([
                'summary' => $summary,
            ]);
    }
}
