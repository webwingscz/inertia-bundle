<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ComponentResolver;

/**
 * Default component resolver which expects final paths to the components.
 */
readonly class DefaultComponentResolver implements InertiaComponentResolverInterface
{
    public function resolve(string $component): string
    {
        return $component;
    }
}
