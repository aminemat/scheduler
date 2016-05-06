<?php

namespace Scheduler\Domain\Shift\Action;

use Equip\Adr\DomainInterface;
use Equip\Adr\PayloadInterface;
use Equip\Adr\Status;
use Respect\Validation\Exceptions\NestedValidationException;
use Scheduler\Domain\Shift\Contract\ShiftRepositoryInterface;
use Respect\Validation\Validator;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;
use Scheduler\Middleware\UserExtractor;
use Scheduler\Persistence\ShiftFactory;

class ShiftUpdater implements DomainInterface
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
     * @param ShiftFactory             $shiftFactory
     * @param PayloadInterface         $payload
     * @param UserRepositoryInterface  $userRepository
     */
    public function __construct(
        ShiftRepositoryInterface $shiftRepository,
        ShiftFactory $shiftFactory,
        PayloadInterface $payload,
        UserRepositoryInterface $userRepository
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
            $shift = $this->shiftRepository->findOneById((string) $input['id']);

            if (!empty($input['start_time'])) {
                Validator::date()->assert($input['start_time']);
                $shift->setStartTime(new \DateTime($input['start_time']));
            }

            if (!empty($input['end_time'])) {
                Validator::date()->assert($input['end_time']);
                $shift->setEndTime(new \DateTime($input['end_time']));
            }

            if (!empty($input['employee_id'])) {
                $employee = $this->userRepository->findOneById((string) $input['employee_id']);
                Validator::stringType()->noWhitespace()->notEmpty()->assert($input['employee_id']);
                $shift->setEmployee($employee);
            }

            /** @var User $manager */
            $manager = $this->userRepository->findOneById($input[UserExtractor::USER_ATTRIBUTE]);
            $this->shiftRepository->updateShift($manager, $shift);
        } catch (NestedValidationException $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_BAD_REQUEST)
                ->withOutput([
                    'errors' => $exception->getMessages(),
                ]);
        } catch (\Exception $exception) {
            return $this->payload
                ->withStatus(Status::STATUS_INTERNAL_SERVER_ERROR)
                ->withOutput([
                    'errors' => [
                        $exception->getMessage(),
                    ],
                ]);
        }

        return $this->payload
            ->withStatus(Status::STATUS_OK)
            ->withOutput([
                'shift' => $shift,
            ]);
    }
}
