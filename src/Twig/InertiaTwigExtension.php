<?php

namespace Webwings\InertiaBundle\Twig;

use Closure;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;
use Webwings\InertiaBundle\InertiaPage;
use Webwings\InertiaBundle\Service\InertiaInterface;

class InertiaTwigExtension extends AbstractExtension
{
    public function __construct(private readonly InertiaInterface $inertia)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'inertia',
                Closure::fromCallable([$this, 'inertiaFunction'])
            ),
            new TwigFunction(
                'inertiaHead',
                Closure::fromCallable([$this, 'inertiaHeadFunction'])
            ),
        ];
    }

    /**
     * @throws ExceptionInterface
     */
    public function inertiaFunction(InertiaPage $page): Markup
    {
        $pageJson = htmlspecialchars($this->inertia->serialize($page));

        return new Markup(
            <<<HTML
            <div id="app" data-page="{$pageJson}"></div>
            HTML,
            'UTF-8'
        );
    }

    public function inertiaHeadFunction(InertiaPage $page): Markup
    {
        return new Markup('', 'UTF-8');
    }
}
