<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\Prop;

/**
 * Loaded asynchronously after initial page render for performance.
 */
class DeferProp extends CallbackProp implements MergeablePropInterface, PartialLoadPropInterface
{
    use MergeablePropTrait;

    public const string DEFAULT_GROUP = 'default';

    public function __construct(callable $callback, public readonly string $group = self::DEFAULT_GROUP)
    {
        parent::__construct($callback);
    }
}
