<?php

declare(strict_types=1);

namespace Webwings\InertiaBundle\DependencyInjection;

use Exception;
use Illuminate\Support\Arr;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Webwings\InertiaBundle\PropProvider\InertiaPropProviderInterface;

class InertiaExtension extends ConfigurableExtension
{
    /**
     * @param  array<string, mixed> $mergedConfig
     * @throws Exception
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $container
            ->registerForAutoconfiguration(InertiaPropProviderInterface::class)
            ->addTag('inertia.prop_provider');

        $container
            ->getDefinition('inertia.service')
            ->setArgument('$componentResolver', new Reference($mergedConfig['component_resolver']))
            ->setArgument('$componentLocator', new Reference($mergedConfig['component_locator']));

        foreach (Arr::dot($mergedConfig, 'inertia.') as $key => $value) {
            $container->setParameter($key, $value);
        }
    }
}
