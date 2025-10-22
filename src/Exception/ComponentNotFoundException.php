<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\Exception;

use InvalidArgumentException;
use Throwable;

class ComponentNotFoundException extends InvalidArgumentException implements InertiaExceptionInterface
{
    public function __construct(
        public readonly string $component,
        string|null $message = null,
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct(
            $message ?? "Inertia page component not found: {$this->component}",
            $code,
            $previous,
        );
    }
}
