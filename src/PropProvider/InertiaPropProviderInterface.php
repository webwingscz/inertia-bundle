<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\PropProvider;

use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\Prop\PropInterface;

/**
 * A class that provides global Inertia props.
 */
interface InertiaPropProviderInterface
{
    /**
     * @return array<string, PropInterface>
     */
    public function getInertiaProps(InertiaHeaders $headers): array;
}
