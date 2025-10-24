<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Attempts to handle a Throwable by producing a Response by first compatible response factory.
 */
readonly class InertiaResponseFactory
{
    /**
     * @param iterable<InertiaResponseFactoryInterface<Throwable>> $responseFactories
     */
    public function __construct(private iterable $responseFactories)
    {
    }

    public function handle(Request $request, Throwable $throwable): Response|null
    {
        foreach ($this->responseFactories as $responseFactory) {
            $handledThrowable = $responseFactory->isHandling($request, $throwable);

            if ($handledThrowable !== null) {
                return $responseFactory->handle($request, $handledThrowable);
            }
        }

        return null;
    }
}
