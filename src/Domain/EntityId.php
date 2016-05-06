<?php

namespace Scheduler\Domain;

use Ramsey\Uuid\Uuid;

class EntityId
{
    /**
     * @var string
     */
    private $value;

    /**
     * UserId constructor.
     *
     * @param string $value
     */
    public function __construct($value = null)
    {
        $this->value = $value ?: (string) Uuid::uuid4()->toString();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
