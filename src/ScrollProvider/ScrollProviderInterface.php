<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ScrollProvider;

/**
 * Provides both data and metadata for infinite scroll.
 */
interface ScrollProviderInterface extends ScrollMetadataProviderInterface
{
    /**
     * @param string|null $wrapper key to put the items under, null to return the items unwrapped
     */
    public function getData(string|null $wrapper): mixed;
}
