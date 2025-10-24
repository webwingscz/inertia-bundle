<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ResponseFactory;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * A response factory trait that provides the means to redirect to the previous page.
 */
trait RedirectBackResponseFactoryTrait
{
    public function redirectBack(Request $request, string $fallbackUrl = '/'): RedirectResponse
    {
        return new RedirectResponse($request->headers->get('referer', $fallbackUrl));
    }
}
