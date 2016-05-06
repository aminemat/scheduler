<?php

namespace Test\Responder;

use Equip\Adr\Status;
use Equip\Payload;
use Mockery as m;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Domain\EntityId;
use Scheduler\Domain\Shift\Shift;
use Scheduler\Domain\User\User;
use Scheduler\Responder\ShiftResponder;
use Scheduler\Transformer\ShiftTransformer;
use Zend\Diactoros\Response\JsonResponse;

class ShiftResponderTest extends \PHPUnit_Framework_TestCase
{

    public function test_returns_a_list_of_errors_if_errors_are_present_in_the_payload()
    {
        $transformerMock = m::mock(ShiftTransformer::class);
        $shiftResponder = new ShiftResponder($transformerMock);

        $requestMock = m::mock(ServerRequestInterface::class);
        $responseMock = m::mock(ResponseInterface::class);
        $payload = (new Payload())
            ->withOutput([
                'errors' => [
                    'error_1' => 'something happened'
                ]
            ])
            ->withStatus(Status::STATUS_BAD_REQUEST);

        /** @var ResponseInterface $actualResponse */
        $actualResponse = $shiftResponder($requestMock, $responseMock, $payload);

        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertEquals(400, $actualResponse->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"errors":{"error_1":"something happened"}}',
            (string)$actualResponse->getBody()
        );
    }

    public function test_returns_a_valid_shift_object()
    {
        $transformerMock = m::mock(ShiftTransformer::class);
        $transformerMock->shouldReceive('transform')->once()->andReturn();
        $shiftResponder = new ShiftResponder($transformerMock);

        $dbData = [
            'id' => 'id',
            'start_time' => '2016-05-18 16:00:00',
            'end_time' => '2016-05-18 20:00:00',
            'manager_id' => 'manager_1',
            'break' => 0.5,
            'employee_id' => 'employee_1',
        ];

        $shift = new Shift(
            new EntityId($dbData['id']),
            new \DateTime($dbData['start_time']),
            new \DateTime($dbData['end_time']),
            m::mock(User::class),
            $dbData['break'],
            m::mock(User::class)
        );

        $requestMock = m::mock(ServerRequestInterface::class);
        $responseMock = m::mock(ResponseInterface::class);
        $payload = (new Payload())
            ->withOutput([
                'shift' => $shift
            ]);
        $actualResponse = $shiftResponder($requestMock, $responseMock, $payload);

        $this->assertInstanceOf(JsonResponse::class, $actualResponse);
        $this->assertEquals(200, $actualResponse->getStatusCode());
    }
}
