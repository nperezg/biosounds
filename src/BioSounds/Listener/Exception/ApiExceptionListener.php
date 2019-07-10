<?php

namespace BioSounds\Listener\Exception;

use BioSounds\Exception\ForbiddenException;
use BioSounds\Exception\NotAuthenticatedException;

class ApiExceptionListener implements ExceptionListenerInterface
{
    public function handleException(\Throwable $throwable)
    {
        error_log($throwable);

        $httpCode = 400;
        if ($throwable instanceof NotAuthenticatedException) {
            $httpCode = 401;
        }

        if ($throwable instanceof ForbiddenException) {
            $httpCode = 403;
        }

        http_response_code($httpCode);

        echo json_encode([
            'errorCode' => $throwable->getCode(),
            'message' => $throwable->getMessage(),
        ]);
    }
}
