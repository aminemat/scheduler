<?php

namespace Scheduler\Persistence;

use Scheduler\Domain\EntityId;
use Scheduler\Domain\Shift\Contract\ShiftFactoryInterface;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;

class ShiftFactory implements ShiftFactoryInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * ShiftFactory constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function fromDBData(array $data)
    {
        return new Shift(
            new EntityId($data['id']),
            new \DateTime($data['start_time']),
            new \DateTime($data['end_time']),
            $this->userRepository->findOneById($data['manager_id']),
            $data['break'],
            $this->userRepository->findOneById($data['employee_id'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fromInputData(array $data)
    {
        /** @var User $manager */
        $manager = $data['manager'];

        return new Shift(
            new EntityId(),
            new \DateTime($data['start_time']),
            new \DateTime($data['end_time']),
            $manager,
            $data['break'],
            $this->userRepository->findOneById($data['employee_id'])
        );
    }
}
