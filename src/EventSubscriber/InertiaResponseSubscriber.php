<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webwings\InertiaBundle\InertiaHeaders;

class InertiaResponseSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly bool $debug)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $headers = InertiaHeaders::fromRequest($request);

        if ($headers->isInertiaRequest() === false) {
            return;
        }

        $response = $event->getResponse();

        if ($this->debug && $request->isXmlHttpRequest()) {
            /*
             * Refreshes the toolbar when the request is an AJAX request.
             */
            $response->headers->set('Symfony-Debug-Toolbar-Replace', '1');
        }

        if (
            $response->isRedirect()
            && Response::HTTP_FOUND === $response->getStatusCode()
            && in_array($request->getMethod(), [
                'PUT',
                'PATCH',
                'DELETE',
            ])
        ) {
            /*
             * If the response is a redirect and the request method is PUT, PATCH, or DELETE, we need to change the status code to 303.
             * @see https://inertiajs.com/redirects
             */
            $response->setStatusCode(Response::HTTP_SEE_OTHER);
        }
    }
}
