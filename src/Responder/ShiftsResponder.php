<?php

namespace Scheduler\Responder;

use Equip\Adr\PayloadInterface;
use Equip\Adr\ResponderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Transformer\ShiftTransformer;
use Zend\Diactoros\Response\JsonResponse;

class ShiftsResponder implements ResponderInterface
{
    /**
     * @var ShiftTransformer
     */
    private $shiftTransformer;

    /**
     * ShiftResponder constructor.
     *
     * @param ShiftTransformer $shiftTransformer
     */
    public function __construct(ShiftTransformer $shiftTransformer)
    {
        $this->shiftTransformer = $shiftTransformer;
    }

    /**
     * Handle marshalling a payload into an HTTP response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param PayloadInterface       $payload
     *
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        PayloadInterface $payload
    ) {
        $shifts = $payload->getOutput()['shifts'];

        return new JsonResponse([
            'shifts' => current($this->shiftTransformer->transformCollection($shifts)),
            'metadata' => [
                'count' => count($shifts),
            ],
        ]);
    }
}
