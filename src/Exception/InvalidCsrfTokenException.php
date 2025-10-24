<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\Exception;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class InvalidCsrfTokenException extends AccessDeniedHttpException implements InertiaExceptionInterface
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(
        string $message = 'Invalid Inertia CSRF token',
        Throwable|null $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct($message, $previous, $code, $headers);
    }
}
