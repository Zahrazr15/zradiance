<?php

namespace App\Exceptions;

use RuntimeException;

class ShippingServiceException extends RuntimeException
{
    public const RATE_LIMITED = 'rate_limited';
    public const QUOTA_EXHAUSTED = 'quota_exhausted';
    public const CONNECTION_FAILED = 'connection_failed';
    public const API_ERROR = 'api_error';

    public function __construct(
        string $message,
        public readonly string $reason,
        public readonly ?int $httpStatus = null,
        public readonly ?int $retryAfterSeconds = null
    ) {
        parent::__construct($message);
    }
}
