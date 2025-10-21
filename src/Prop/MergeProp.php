<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\Prop;

/**
 * Merged with existing client-side data during partial reloads.
 */
class MergeProp extends BasicProp implements MergeablePropInterface
{
    use MergeablePropTrait;

    public function __construct(mixed $value)
    {
        parent::__construct($value);
        $this->merge();
    }
}
