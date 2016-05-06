<?php

namespace Scheduler\Domain\User\Contract;

interface TokenRepositoryInterface
{
    /**
     * @param string $token
     *
     * @return bool
     */
    public function tokenExists($token);

    /**
     * @param $token
     * @param $userId
     *
     * @return mixed
     */
    public function saveToken($token, $userId);
}
