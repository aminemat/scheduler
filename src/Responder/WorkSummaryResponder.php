<?php

namespace Scheduler\Responder;

use Equip\Adr\PayloadInterface;
use Equip\Adr\ResponderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Scheduler\Domain\Shift\WeeklySummary;
use Zend\Diactoros\Response\JsonResponse;

class WorkSummaryResponder implements ResponderInterface
{
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
        /** @var WeeklySummary $summary */
        $summary = $payload->getOutput()['summary'];
        $workedWeeks = $summary->getWorkedWeeks();

        $data = [
            'employee' => (string) $summary->getEmployee()->getName(),
        ];

        foreach ($workedWeeks as $workedWeek) {
            $data['summary'][] = [
                'year' => substr($workedWeek->getWeekNumber(), 0, 4),
                'week' => substr($workedWeek->getWeekNumber(), 4),
                'hours' => (int) $workedWeek->getWorkedHours() / 3600,
            ];
        }

        return new JsonResponse($data);
    }
}
