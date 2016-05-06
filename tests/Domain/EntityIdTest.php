<?php

namespace Test\Domain;

use Scheduler\Domain\EntityId;

class EntityIdTest extends \PHPUnit_Framework_TestCase
{
    public function test_creates_a_valid_UUID_if_a_value_is_not_passed()
    {
        $id = new EntityId();
        $this->assertEquals(36, strlen($id));
    }

    public function uses_the_id_passed_as_argument()
    {
        $id = new EntityId('ID');
        $this->assertEquals('ID', $id);
    }
}
