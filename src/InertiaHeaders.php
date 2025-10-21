<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

readonly class InertiaHeaders
{
    /**
     * The main Inertia request header.
     */
    public const string INERTIA = 'X-Inertia';

    /**
     * Header for external redirects.
     */
    public const string LOCATION = 'X-Inertia-Location';

    /**
     * Header for the current asset version.
     */
    public const string VERSION = 'X-Inertia-Version';

    /**
     * Header specifying the component for partial reloads.
     */
    public const string PARTIAL_COMPONENT = 'X-Inertia-Partial-Component';

    /**
     * Header specifying which props to include in partial reloads.
     */
    public const string PARTIAL_ONLY = 'X-Inertia-Partial-Data';

    /**
     * Header specifying which props to exclude from partial reloads.
     */
    public const string PARTIAL_EXCEPT = 'X-Inertia-Partial-Except';

    /**
     * Header for resetting the page state.
     */
    public const string RESET = 'X-Inertia-Reset';

    /**
     * Header for specifying the merge intent when paginating on infinite scroll.
     */
    public const string INFINITE_SCROLL_MERGE_INTENT = 'X-Inertia-Infinite-Scroll-Merge-Intent';

    /**
     * Header for specifying which error bag to use for validation errors.
     */
    public const ERROR_BAG = 'X-Inertia-Error-Bag';

    public function __construct(protected HeaderBag $headers)
    {
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->headers);
    }

    public function isInertiaRequest(): bool
    {
        return $this->headers->has(self::INERTIA);
    }

    public function isPartialRequest(string $component): bool
    {
        return $this->headers->get(self::PARTIAL_COMPONENT) === $component;
    }

    /**
     * @return list<string>
     */
    public function getPartialOnlyProps(): array
    {
        return $this->parseCommaSeparatedHeaderValue(self::PARTIAL_ONLY);
    }

    /**
     * @return list<string>
     */
    public function getPartialExceptProps(): array
    {
        return $this->parseCommaSeparatedHeaderValue(self::PARTIAL_EXCEPT);
    }

    /**
     * @return list<string>
     */
    public function getResetProps(): array
    {
        return $this->parseCommaSeparatedHeaderValue(self::RESET);
    }

    /**
     * @return 'prepend'|'append'
     */
    public function getMergeIntent(): string
    {
        return match ($this->headers->get(self::INFINITE_SCROLL_MERGE_INTENT)) {
            'prepend' => 'prepend',
            default => 'append',
        };
    }

    public function getVersion(): string|null
    {
        return $this->headers->get(self::VERSION);
    }

    public function getErrorBag(): string|null
    {
        return $this->headers->get(self::ERROR_BAG);
    }

    /**
     * @return list<string>
     */
    private function parseCommaSeparatedHeaderValue(string $header): array
    {
        return array_values(array_filter(explode(',', $this->headers->get($header, ''))));
    }
}
