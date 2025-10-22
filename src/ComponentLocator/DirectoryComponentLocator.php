<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ComponentLocator;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Webwings\InertiaBundle\Exception\ComponentLocatorException;

use function Symfony\Component\String\b;

/**
 * Component locator that locates Inertia components inside a filesystem directory.
 */
readonly class DirectoryComponentLocator implements InertiaComponentLocatorInterface
{
    public function __construct(
        protected Filesystem $filesystem,
        protected string $directory,
        protected string $extension = '.vue',
    ) {
    }

    public function exists(string $component): bool
    {
        $componentPath = b($this->directory)
            ->append('/', $component)
            ->append($this->extension);

        try {
            return $this->filesystem->exists($componentPath->toString());
        } catch (IOException $e) {
            throw new ComponentLocatorException(previous: $e);
        }
    }
}
