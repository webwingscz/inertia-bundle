<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Webwings\InertiaBundle\Exception\InvalidCsrfTokenException;

/**
 * Default CSRF error response factory that redirects to the previous page.
 *
 * @implements InertiaResponseFactoryInterface<InvalidCsrfTokenException>
 */
readonly class InvalidCsrfTokenResponseFactory implements InertiaResponseFactoryInterface
{
    use RedirectBackResponseFactoryTrait;
    use ExtractThrowableResponseFactoryTrait;

    public static function getPriority(): int
    {
        return -255;
    }

    public function isHandling(Request $request, Throwable $throwable): Throwable|null
    {
        return $this->extractThrowable($throwable, InvalidCsrfTokenException::class);
    }

    public function handle(Request $request, Throwable $throwable): Response
    {
        return $this->redirectBack($request);
    }
}
