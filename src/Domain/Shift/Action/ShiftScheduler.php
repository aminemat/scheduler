<?php

namespace Scheduler\Domain\Shift\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Exception;
use InvalidArgumentException;
use Scheduler\Domain\Shift\Contract\ShiftFactoryInterface;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Respect\Validation\Validator;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Middleware\UserExtractor;
use Scheduler\Persistence\ShiftFactory;

class ShiftScheduler implements DomainInterface
{
    private $payload;

    /**
     * @var ShiftRepositoryInterface
     */
    private $shiftRepository;
    /**
     * @var ShiftFactory
     */
    private $shiftFactory;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * ShiftProvider constructor.
     *
     * @param ShiftRepositoryInterface $shiftRepository
     * @param UserRepositoryInterface  $userRepository
     * @param ShiftFactoryInterface    $shiftFactory
     * @param PayloadInterface         $payload
     */
    public function __construct(
        ShiftRepositoryInterface $shiftRepository,
        UserRepositoryInterface $userRepository,
        ShiftFactoryInterface $shiftFactory,
        PayloadInterface $payload
    ) {
        $this->payload = $payload;
        $this->shiftRepository = $shiftRepository;
        $this->shiftFactory = $shiftFactory;
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
            Validator::stringType()->noWhitespace()->notEmpty()->assert($input['employee_id']);
            Validator::date()->notEmpty()->assert($input['start_time']);
            Validator::date()->notEmpty()->assert($input['end_time']);
            Validator::floatVal()->assert($input['break']);

            /** @var User $manager */
            $manager = $this->userRepository->findOneById($input[UserExtractor::USER_ATTRIBUTE]);

            $data = [
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
                'manager' => $manager,
                'employee_id' => $input['employee_id'],
                'break' => $input['break'],
            ];

            $shift = $this->shiftFactory->fromInputData($data);
            $this->shiftRepository->saveShift($manager, $shift);
        } catch (InvalidArgumentException $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_BAD_REQUEST)
                ->withOutput([
                    'errors' => $exception->getMessage(),
                ]);
        } catch (Exception $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_INTERNAL_SERVER_ERROR)
                ->withOutput([
                    'errors' => [
                        'User not found',
                    ],
                ]);
        }

        return $this->payload
            ->withStatus(Status::STATUS_CREATED)
            ->withOutput([
                'shift' => $shift,
            ]);
    }
}
