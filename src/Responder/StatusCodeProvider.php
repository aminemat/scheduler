<?php

namespace Scheduler\Responder;

use Equip\Adr\Status;
use Lukasoppermann\Httpstatus\Httpstatuscodes;

/**
 * Class StatusCodeProvider.
 */
class StatusCodeProvider implements Httpstatuscodes, Status
{
    const DEFAULT_STATUS = self::HTTP_OK;

    /**
     * Provides a quick dirty way to translates from an ADR payload status
     * to a HTTP status while waiting for official fixes in equip 2.0.
     *
     * @see https://github.com/equip/framework/pull/9
     *
     * @param $payloadStatus
     *
     * @return int
     */
    public static function fromPayloadStatus($payloadStatus)
    {
        $mapping = [
            Status::STATUS_OK => Httpstatuscodes::HTTP_OK,
            Status::STATUS_UNAUTHORIZED => Httpstatuscodes::HTTP_UNAUTHORIZED,
            Status::STATUS_BAD_REQUEST => Httpstatuscodes::HTTP_BAD_REQUEST,
            Status::STATUS_INTERNAL_SERVER_ERROR => Httpstatuscodes::HTTP_INTERNAL_SERVER_ERROR,
            Status::STATUS_CREATED => Httpstatuscodes::HTTP_CREATED,
        ];

        return array_key_exists($payloadStatus, $mapping)
            ? $mapping[$payloadStatus]
            : self::DEFAULT_STATUS;
    }
}
