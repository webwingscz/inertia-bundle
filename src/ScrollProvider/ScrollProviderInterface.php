<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ScrollProvider;

/**
 * Provides both data and metadata for infinite scroll.
 */
interface ScrollProviderInterface extends ScrollMetadataProviderInterface
{
    public function getData(string $wrapper): mixed;
}
