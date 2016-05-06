<?php

namespace Scheduler\Transformer;

use Scheduler\Domain\User\User;

class UserTransformer
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => (string) $user->getId(),
            'name' => (string) $user->getName(),
            'email' => (string) $user->getEmail(),
            'phone' => (string) $user->getPhone(),
        ];
    }
}
