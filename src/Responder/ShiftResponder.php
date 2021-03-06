<?php

namespace Scheduler\Responder;

use Equip\Adr\PayloadInterface;
use Equip\Adr\ResponderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Transformer\ShiftTransformer;
use Zend\Diactoros\Response\JsonResponse;

class ShiftResponder implements ResponderInterface
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
        $output = $payload->getOutput();
        if ($this->hasErrors($output)) {
            return new JsonResponse([
                'errors' => $output['errors'],
            ], StatusCodeProvider::fromPayloadStatus($payload->getStatus()));
        }

        return new JsonResponse($this->shiftTransformer->transform($output['shift']));
    }

    /**
     * @param array $output
     *
     * @return bool
     */
    private function hasErrors(array $output)
    {
        return array_key_exists('errors', $output);
    }
}
