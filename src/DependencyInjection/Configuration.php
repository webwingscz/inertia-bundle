<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Webwings\InertiaBundle\EventSubscriber\InertiaCsrfSubscriber;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('inertia');
        $treeBuilder
            ->getRootNode()
            ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('root_view')
                    ->defaultValue('base.html.twig')
                    ->end()
                    ->scalarNode('component_resolver')
                    ->defaultValue('inertia.component_resolver.default')
                    ->end()
                    ->scalarNode('component_locator')
                    ->defaultValue('inertia.component_locator.default')
                    ->end()
                    ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')
                            ->defaultTrue()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('csrf')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('enabled')
                            ->defaultTrue()
                            ->end()
                            ->scalarNode('token_name')
                            ->defaultValue(InertiaCsrfSubscriber::DEFAULT_CSRF_TOKEN_NAME)
                            ->end()
                            ->scalarNode('header_name')
                            ->defaultValue(InertiaCsrfSubscriber::DEFAULT_CSRF_HEADER_NAME)
                            ->end()
                            ->arrayNode('cookie')
                            ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('name')
                                    ->defaultValue(InertiaCsrfSubscriber::DEFAULT_CSRF_COOKIE_NAME)
                                    ->end()
                                    ->scalarNode('expire')
                                    ->defaultValue(0)
                                    ->end()
                                    ->scalarNode('path')
                                    ->defaultValue('/')
                                    ->end()
                                    ->scalarNode('domain')
                                    ->defaultValue(null)
                                    ->end()
                                    ->scalarNode('secure')
                                    ->defaultValue(false)
                                    ->end()
                                    ->scalarNode('raw')
                                    ->defaultValue(false)
                                    ->end()
                                    ->scalarNode('samesite')
                                    ->defaultValue(Cookie::SAMESITE_LAX)
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
