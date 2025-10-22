<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ComponentLocator;

use Webwings\InertiaBundle\Exception\ComponentLocatorException;

interface InertiaComponentLocatorInterface
{
    /**
     * Check that Inertia component actually exists.
     *
     * @throws ComponentLocatorException
     */
    public function exists(string $component): bool;
}
