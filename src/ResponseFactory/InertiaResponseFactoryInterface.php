<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * A class that can transform Throwable to a Response.
 *
 * @template T of Throwable
 */
interface InertiaResponseFactoryInterface
{
    /**
     * Get priority by which the factories are evaluated.
     */
    public static function getPriority(): int;

    /**
     * Return throwable to handle if this factory can handle the supplied throwable.
     *
     * @return T|null
     */
    public function isHandling(Request $request, Throwable $throwable): Throwable|null;

    /**
     * Handle the throwable by creating a response.
     *
     * @param T $throwable
     */
    public function handle(Request $request, Throwable $throwable): Response;
}
