<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle;

use Illuminate\Support\Collection;
use JsonSerializable;
use Webwings\InertiaBundle\Prop\AlwaysProp;
use Webwings\InertiaBundle\Prop\BasicProp;
use Webwings\InertiaBundle\Prop\DeferProp;
use Webwings\InertiaBundle\Prop\MergeablePropInterface;
use Webwings\InertiaBundle\Prop\PartialLoadPropInterface;
use Webwings\InertiaBundle\Prop\PropInterface;

readonly class InertiaPage implements JsonSerializable
{
    /** @var Collection<string, PropInterface> */
    protected Collection $props;

    /**
     * @param array<string, mixed> $props
     */
    public function __construct(
        public InertiaHeaders $headers,
        public string $component,
        array $props = [],
        public string|null $url = null,
        public string|null $version = null,
        public bool $clearHistory = false,
        public bool $encryptHistory = false,
    ) {
        $this->props = collect($props)
            ->map(fn ($prop) => $prop instanceof PropInterface ? $prop : new BasicProp($prop))
            ->each(fn (PropInterface $prop) => $prop->attachToPage($this));
    }

    /** @return Collection<string, PropInterface> */
    public function getProps(): Collection
    {
        return clone $this->props;
    }

    /**
     * @return Collection<string, MergeablePropInterface>
     */
    public function filterMergeableProps(bool $rejectResetProps = true): Collection
    {
        $resetProps = $rejectResetProps ? $this->headers->getResetProps() : [];
        $onlyProps = $this->headers->getPartialOnlyProps();
        $exceptProps = $this->headers->getPartialExceptProps();

        /** @var Collection<string, MergeablePropInterface> $mergeProps */
        $mergeProps = $this->props->filter(fn (PropInterface $prop) => $prop instanceof MergeablePropInterface);

        return $mergeProps
            ->filter(fn (MergeablePropInterface $prop) => $prop->shouldMerge())
            ->reject(fn ($_, string $key) => in_array($key, $resetProps))
            ->filter(fn ($_, string $key) => count($onlyProps) === 0 || in_array($key, $onlyProps))
            ->reject(fn ($_, string $key) => in_array($key, $exceptProps));
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'component' => $this->component,
            'version' => $this->version,
            'url' => $this->url,
            'clearHistory' => $this->clearHistory,
            'encryptHistory' => $this->encryptHistory,
            'props' => $this->resolveProps(),
            ...$this->resolveMergeProps(),
            ...$this->resolveDeferredProps(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function resolveProps(): array
    {
        $resolvedProps = clone $this->props;
        $resolvedProps = $this->resolvePartialProps($resolvedProps);
        $resolvedProps = $this->resolveAlwaysProps($resolvedProps);
        $resolvedProps = $this->resolvePropertyObjects($resolvedProps);

        return $resolvedProps->toArray();
    }

    /**
     * @param  Collection<string, PropInterface> $props
     * @return Collection<string, PropInterface>
     */
    public function resolvePartialProps(Collection $props): Collection
    {
        if (! $this->headers->isPartialRequest($this->component)) {
            return $props->reject(fn (PropInterface $prop) => $prop instanceof PartialLoadPropInterface);
        }

        $only = $this->headers->getPartialOnlyProps();
        $except = $this->headers->getPartialExceptProps();

        if ($only) {
            $props = $props->only($only);
        }

        if ($except) {
            $props = $props->except($except);
        }

        return $props;
    }

    /**
     * @param  Collection<string, PropInterface> $props
     * @return Collection<string, PropInterface>
     */
    public function resolveAlwaysProps(Collection $props): Collection
    {
        return $this->props
            ->filter(fn (PropInterface $prop) => $prop instanceof AlwaysProp)
            ->merge($props);
    }

    /**
     * @param  Collection<string, PropInterface> $props
     * @return Collection<string, mixed>
     */
    public function resolvePropertyObjects(Collection $props): Collection
    {
        return $props->map(fn (PropInterface $prop) => $prop->resolveValue($this));
    }

    /**
     * @return array{
     *     mergeProps?: array<int, string>,
     *     prependProps?: array<int, string>,
     *     deepMergeProps?: array<int, string>,
     *     matchPropsOn?: array<int, string>
     * }
     */
    public function resolveMergeProps(): array
    {
        $mergeProps = $this->filterMergeableProps();

        return array_filter([
            'mergeProps' => $this->resolveAppendMergeProps($mergeProps),
            'prependProps' => $this->resolvePrependMergeProps($mergeProps),
            'deepMergeProps' => $this->resolveDeepMergeProps($mergeProps),
            'matchPropsOn' => $this->resolveMergeMatchingKeys($mergeProps),
        ], fn ($prop) => count($prop) > 0);
    }

    /**
     * @param  Collection<string, MergeablePropInterface> $mergeProps
     * @return array<int, string>
     */
    protected function resolveAppendMergeProps(Collection $mergeProps): array
    {
        [$rootAppendProps, $nestedAppendProps] = $mergeProps
            ->reject(fn (MergeablePropInterface $prop) => $prop->shouldDeepMerge())
            ->partition(fn (MergeablePropInterface $prop) => $prop->appendsAtRoot());

        return $nestedAppendProps
            ->flatMap(fn (MergeablePropInterface $prop, string $key) => collect($prop->appendsAtPaths())
                ->map(fn ($path) => $key.'.'.$path),
            )
            ->merge($rootAppendProps->keys()->toArray())
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * @param  Collection<string, MergeablePropInterface> $mergeProps
     * @return array<int, string>
     */
    protected function resolvePrependMergeProps(Collection $mergeProps): array
    {
        [$rootPrependProps, $nestedPrependProps] = $mergeProps
            ->reject(fn (MergeablePropInterface $prop) => $prop->shouldDeepMerge())
            ->partition(fn (MergeablePropInterface $prop) => $prop->prependsAtRoot());

        return $nestedPrependProps
            ->flatMap(fn (MergeablePropInterface $prop, string $key) => collect($prop->prependsAtPaths())
                ->map(fn ($path) => $key.'.'.$path),
            )
            ->merge($rootPrependProps->keys()->toArray())
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * @param  Collection<string, MergeablePropInterface> $mergeProps
     * @return array<int, string>
     */
    protected function resolveDeepMergeProps(Collection $mergeProps): array
    {
        return $mergeProps
            ->filter(fn (MergeablePropInterface $prop) => $prop->shouldDeepMerge())
            ->keys()
            ->toArray();
    }

    /**
     * Resolve the matching keys for merge props.
     *
     * @param  Collection<string, MergeablePropInterface> $mergeProps
     * @return array<int, string>
     */
    protected function resolveMergeMatchingKeys(Collection $mergeProps): array
    {
        return $mergeProps
            ->map(function (MergeablePropInterface $prop, $key) {
                return collect($prop->matchesOn())
                    ->map(fn ($strategy) => $key.'.'.$strategy)
                    ->toArray();
            })
            ->flatten()
            ->values()
            ->toArray();
    }

    /**
     * @return array{deferredProps?: array<string, mixed>}
     */
    public function resolveDeferredProps(): array
    {
        if ($this->headers->isPartialRequest($this->component)) {
            return [];
        }

        /** @var Collection<string, DeferProp> $deferredProps */
        $deferredProps = $this->props->filter(fn (PropInterface $prop) => $prop instanceof DeferProp);
        $deferredProps = $deferredProps
            ->map(fn (DeferProp $prop, string $key) => ['key' => $key, 'group' => $prop->group])
            ->groupBy('group')
            ->map
            ->pluck('key');

        return $deferredProps->isNotEmpty() ? ['deferredProps' => $deferredProps->toArray()] : [];
    }
}
