<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Throwable;

/**
 * A response factory trait that provides the means to extract a specific Throwable from the given Throwable.
 */
trait ExtractThrowableResponseFactoryTrait
{
    /**
     * @template T of Throwable
     * @param class-string<T> ...$throwableClasses
     * @phpstan-return T|null
     */
    public function extractThrowable(Throwable $throwable, string ...$throwableClasses): Throwable|null
    {
        if (empty($throwableClasses)) {
            return null;
        }

        while ($throwable !== null) {
            foreach ($throwableClasses as $exceptionClass) {
                if (is_a($throwable, $exceptionClass, true)) {
                    return $throwable;
                }
            }

            $throwable = $throwable->getPrevious();
        }

        return null;
    }
}
