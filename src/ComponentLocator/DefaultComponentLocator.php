<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ComponentLocator;

/**
 * Default component locator that locates components with outstanding success rate.
 */
readonly class DefaultComponentLocator implements InertiaComponentLocatorInterface
{
    public function exists(string $component): bool
    {
        return true;
    }
}
