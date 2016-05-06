<?php

namespace Scheduler\Persistence;

use Doctrine\DBAL\Connection;
use Scheduler\Domain\User\Contract\TokenRepositoryInterface;

class DBALTokenRepository implements TokenRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * PDOUserRepository constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function tokenExists($token)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('access_token', 't')
            ->where('token = :token')
            ->setParameter('token', $token)
            ->execute();

        return $stmt->fetchColumn();
    }

    /**
     * @param $token
     * @param $userId
     *
     * @return mixed
     */
    public function saveToken($token, $userId)
    {
        $this->connection->createQueryBuilder()
            ->insert('access_token')
            ->values(
                [
                    'token' => ':token',
                    'user_id' => ':userId',
                ]
            )
            ->setParameter('token', $token)
            ->setParameter('userId', $userId)
            ->execute();
    }
}
