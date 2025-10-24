<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webwings\InertiaBundle\Exception\InvalidCsrfTokenException;
use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\ResponseFactory\InertiaResponseFactory;

class InertiaCsrfSubscriber implements EventSubscriberInterface
{
    public const string DEFAULT_CSRF_TOKEN_NAME = 'X-Inertia-CSRF-TOKEN';
    public const string DEFAULT_CSRF_HEADER_NAME = 'X-XSRF-TOKEN';
    public const string DEFAULT_CSRF_COOKIE_NAME = 'XSRF-TOKEN';

    /**
     * @phpstan-param Cookie::SAMESITE_* $csrfCookieSamesite
     */
    public function __construct(
        private readonly CsrfTokenManagerInterface $csrfTokenManager,
        private readonly InertiaResponseFactory $responseFactory,
        private readonly bool $csrfEnabled = false,
        private readonly string $csrfTokenName = self::DEFAULT_CSRF_TOKEN_NAME,
        private readonly string $csrfHeaderName = self::DEFAULT_CSRF_HEADER_NAME,
        private readonly string $csrfCookieName = self::DEFAULT_CSRF_COOKIE_NAME,
        private readonly int|string $csrfCookieExpire = 0,
        private readonly string $csrfCookiePath = '/',
        private readonly string|null $csrfCookieDomain = null,
        private readonly bool $csrfCookieSecure = false,
        private readonly bool $csrfCookieRaw = false,
        private readonly string $csrfCookieSamesite = Cookie::SAMESITE_LAX,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $headers = InertiaHeaders::fromRequest($request);

        if ($headers->isInertiaRequest() === false) {
            return;
        }

        if ($this->shouldValidateOrGenerateCsrf($event) === false) {
            return;
        }

        $csrfToken = new CsrfToken($this->csrfTokenName, $request->headers->get($this->csrfHeaderName));

        if ($this->csrfTokenManager->isTokenValid($csrfToken) === false) {
            $throwable = new InvalidCsrfTokenException();
            $response = $this->responseFactory->handle($request, $throwable) ?? throw $throwable;

            $event->setResponse($response);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->shouldValidateOrGenerateCsrf($event) === false) {
            return;
        }

        $cookie = new Cookie(
            $this->csrfCookieName,
            $this->csrfTokenManager->getToken($this->csrfTokenName)->getValue(),
            $this->csrfCookieExpire,
            $this->csrfCookiePath,
            $this->csrfCookieDomain,
            $this->csrfCookieSecure,
            false,
            $this->csrfCookieRaw,
            $this->csrfCookieSamesite,
        );

        $event->getResponse()->headers->setCookie($cookie);
    }

    protected function shouldValidateOrGenerateCsrf(RequestEvent|ResponseEvent $event): bool
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (is_string($route) && str_starts_with($route, '_')) {
            // Ignore requests to internal routes starting with _
            return false;
        }

        return
            $this->csrfEnabled
            && $event->isMainRequest()
            && ! $event->getResponse()?->isRedirect();
    }
}
