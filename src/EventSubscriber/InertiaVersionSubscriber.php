<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webwings\InertiaBundle\InertiaHeaders;
use Webwings\InertiaBundle\Service\InertiaInterface;

class InertiaVersionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly InertiaInterface $inertia)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', -255],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $headers = InertiaHeaders::fromRequest($request);

        if ($headers->isInertiaRequest() === false) {
            return;
        }

        if (
            $request->getMethod() === Request::METHOD_GET
            && $this->inertia->getVersion() !== null
            && $this->inertia->getVersion() !== $headers->getVersion()
        ) {
            $event->setResponse($this->inertia->location($request->getUri()));
        }
    }
}
