<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\ComponentResolver;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\ByteString;
use Webwings\InertiaBundle\Exception\ComponentResolverException;

use function Symfony\Component\String\b;

/**
 * A component resolver which expects a short page name and generates a path using the controller name.
 */
readonly class ControllerNameComponentResolver implements InertiaComponentResolverInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private string $controllerNamespacePrefix = 'App\\Controller\\',
        private string $controllerNameSuffix = 'Controller',
        private string $componentNameSuffix = 'Page',
    ) {
    }

    public function resolve(string $component): string
    {
        $controllerClass = $this->getControllerClass();
        $paths = b($controllerClass)
            ->replace($this->controllerNamespacePrefix, '')
            ->beforeLast('\\')
            ->trim('\\')
            ->split('\\');
        $controller = b($controllerClass)
            ->afterLast('\\')
            ->replace($this->controllerNameSuffix, '');
        $component = b($component);

        return b('/')
            ->join(array_map(fn (ByteString $path) => $path->snake()->toString(), $paths))
            ->append('/', $controller->snake()->toString())
            ->append(
                '/',
                $controller->camel()->title()->toString(),
                $component->camel()->title()->toString(),
                $this->componentNameSuffix,
            )
            ->toString();
    }

    public function getControllerClass(): string
    {
        $request = $this->requestStack->getCurrentRequest()
            ?? throw new ComponentResolverException('Could not obtain current request');

        $controller = $request->attributes->get('_controller');

        if (is_string($controller) === false) {
            throw new ComponentResolverException('Unsupported request controller attribute');
        }

        return explode('::', $controller, 2)[0];
    }
}
