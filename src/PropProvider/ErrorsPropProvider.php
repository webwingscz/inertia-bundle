<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\PropProvider;

use ArrayObject;
use Webwings\InertiaBundle\InertiaFlash;
use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\InertiaProp;
use Webwings\InertiaBundle\Service\InertiaInterface;

/**
 * A prop provider that provides flashed validation errors.
 */
class ErrorsPropProvider implements InertiaPropProviderInterface
{
    public function getInertiaProps(InertiaHeaders $headers, InertiaFlash $flash): array
    {
        return [
            // ArrayObject keeps empty errors serialized as {} instead of []
            InertiaInterface::PROP_ERRORS => InertiaProp::always(new ArrayObject($flash->getErrors())),
        ];
    }
}
