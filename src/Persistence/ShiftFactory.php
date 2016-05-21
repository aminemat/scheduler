<?php

namespace Scheduler\Persistence;

use App\Config\Config;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\Shift\Contract\ShiftFactoryInterface;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\User\Contract\UserRepositoryInterface;
use Scheduler\Domain\User\User;

class ShiftFactory implements ShiftFactoryInterface
{
    const DEFAULT_TIMEZONE = '';
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var Config
     */
    private $config;

    /**
     * ShiftFactory constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param Config $config
     */
    public function __construct(UserRepositoryInterface $userRepository, Config $config)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data)
    {
        $id = !empty($data['id'])
            ? $data['id']
            : null;

        $manager = !empty($data['manager_id'])
            ? $this->userRepository->findOneById($data['manager_id'])
            : $data['manager'];
            
            
        return new Shift(
            new EntityId($id),
            $this->getDateTimeInDefaultTimezone($data['start_time']),
            $this->getDateTimeInDefaultTimezone($data['end_time']),
            $manager,
            $data['break'],
            $this->userRepository->findOneById($data['employee_id'])
        );
    }

    /**
     * @param $date
     * @return \DateTime
     */
    private function getDateTimeInDefaultTimezone($date)
    {
        $dateTime = new \DateTime($date);
        $dateTime->setTimezone(new \DateTimeZone($this->config['DEFAULT_TIMEZONE']));

        return $dateTime;
    }
}
