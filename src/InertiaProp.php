<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle;

use Webwings\InertiaBundle\Prop\AlwaysProp;
use Webwings\InertiaBundle\Prop\BasicProp;
use Webwings\InertiaBundle\Prop\CallbackProp;
use Webwings\InertiaBundle\Prop\DeferProp;
use Webwings\InertiaBundle\Prop\MergeProp;
use Webwings\InertiaBundle\Prop\OptionalProp;
use Webwings\InertiaBundle\Prop\ScrollProp;
use Webwings\InertiaBundle\ScrollProvider\ScrollMetadataProviderInterface;
use Webwings\InertiaBundle\ScrollProvider\ScrollProviderInterface;

readonly class InertiaProp
{
    public static function basic(mixed $value): BasicProp
    {
        return new BasicProp($value);
    }

    public static function callback(callable $value): CallbackProp
    {
        return new CallbackProp($value);
    }

    public static function always(mixed $value): AlwaysProp
    {
        return new AlwaysProp($value);
    }

    public static function optional(callable $callback): OptionalProp
    {
        return new OptionalProp($callback);
    }

    public static function defer(callable $callback, string $group = DeferProp::DEFAULT_GROUP): DeferProp
    {
        return new DeferProp($callback, $group);
    }

    public static function merge(mixed $value): MergeProp
    {
        return new MergeProp($value);
    }

    public static function deepMerge(mixed $value): MergeProp
    {
        return (new MergeProp($value))->deepMerge();
    }

    public static function scroll(
        mixed $value,
        ScrollMetadataProviderInterface $metadata,
        string $wrapper = ScrollProp::DEFAULT_WRAPPER,
    ): ScrollProp {
        return new ScrollProp($value, $metadata, $wrapper);
    }

    public static function scrollProvider(
        ScrollProviderInterface $provider,
        string $wrapper = ScrollProp::DEFAULT_WRAPPER,
    ): ScrollProp {
        return new ScrollProp(fn () => $provider->getData($wrapper), $provider, $wrapper);
    }
}
