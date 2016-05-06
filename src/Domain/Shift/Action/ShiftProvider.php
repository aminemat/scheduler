<?php

namespace Scheduler\Domain\Shift\Action;

use DateTime;
use Equip\Adr\Status;
use Exception;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Middleware\UserExtractor;

class ShiftProvider implements DomainInterface
{
    /**
     * @var PayloadInterface
     */
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
        try {
            $startDate = empty($input['start_date'])
                ? new DateTime('now')
                : new DateTime($input['start_date']);

            $endDate = empty($input['end_date'])
                ? new DateTime('now + 3 days')
                : new DateTime($input['end_date']);

            $coworkers = isset($input['coworkers']) ? true : false;

            $userId = $input[UserExtractor::USER_ATTRIBUTE];
            $user = $this->userRepository->findOneById($userId);

            $shifts = $user->isManager()
                ? $this->shiftRepository->fetchAll($startDate, $endDate, $coworkers)
                : $this->shiftRepository->fetchAllByEmployee($user, $startDate, $endDate, $coworkers);
        } catch (Exception $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_INTERNAL_SERVER_ERROR)
                ->withOutput([
                    'errors' => [
                        'unable to fetch shift',
                    ],
                ]);
        }

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput([
                'shifts' => $shifts,
            ]);
    }
}
