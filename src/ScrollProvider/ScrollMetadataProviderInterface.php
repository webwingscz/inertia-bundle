<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ScrollProvider;

/**
 * Provides necessary metadata for infinite scroll.
 */
interface ScrollMetadataProviderInterface
{
    public const string DEFAULT_PAGE_NAME = 'page';

    /**
     * Get the page name parameter.
     */
    public function getPageName(): string;

    /**
     * Get the previous page identifier.
     */
    public function getPreviousPage(): int|string|null;

    /**
     * Get the next page identifier.
     */
    public function getNextPage(): int|string|null;

    /**
     * Get the current page identifier.
     */
    public function getCurrentPage(): int|string|null;
}
