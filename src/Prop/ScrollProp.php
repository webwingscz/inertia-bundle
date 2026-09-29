<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\Prop;

use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\InertiaPage;
use Webwings\InertiaBundle\ScrollProvider\ScrollMetadataProviderInterface;

/**
 * Represents a paginated property that can be merged during partial reloads.
 *
 * @phpstan-type ScrollPropMetadata array{
 *     pageName: string,
 *     previousPage: int|string|null,
 *     nextPage: int|string|null,
 *     currentPage: int|string|null,
 *     reset: bool,
 * }
 */
class ScrollProp extends BasicProp implements MergeablePropInterface
{
    use MergeablePropTrait;

    public const string DEFAULT_WRAPPER = 'data';

    /**
     * @param string|null $wrapper key holding the items inside the value, null when the value itself is the list of items
     */
    public function __construct(
        mixed $value,
        public readonly ScrollMetadataProviderInterface $metadata,
        public readonly string|null $wrapper = self::DEFAULT_WRAPPER,
    ) {
        parent::__construct($value);
        $this->merge();
    }

    public function attachToPage(InertiaPage $page): void
    {
        parent::attachToPage($page);
        $this->configureMergeIntent($page->headers);
    }

    public function configureMergeIntent(InertiaHeaders $headers): static
    {
        $path = $this->wrapper ?? true;

        return $headers->getMergeIntent() === 'prepend'
            ? $this->prepend($path)
            : $this->append($path);
    }

    /**
     * @return ScrollPropMetadata
     */
    public function metadata(bool $reset = false): array
    {
        return [
            'pageName' => $this->metadata->getPageName(),
            'previousPage' => $this->metadata->getPreviousPage(),
            'nextPage' => $this->metadata->getNextPage(),
            'currentPage' => $this->metadata->getCurrentPage(),
            'reset' => $reset,
        ];
    }
}
