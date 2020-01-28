<?php

/*
 * Created by netlogix GmbH & Co. KG
 *
 * @copyright netlogix GmbH & Co. KG
 */

namespace sdShopEnvironment\Components\CompilerPass;

use sdShopEnvironment\DataTypes\DataTypeInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DataTypeCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sd_shop_environment.data_types.data_type_collector')) {
            return;
        }

        $definition = $container->findDefinition('sd_shop_environment.data_types.data_type_collector');
        $taggedServices = $container->findTaggedServiceIds('sd.data_type');

        foreach ($taggedServices as $id => $tags) {
            $def = $container->getDefinition($id);

            $class = $container->getParameterBag()->resolveValue($def->getClass());

            if ($class instanceof DataTypeInterface) {
                throw new \InvalidArgumentException(
                    \sprintf('Service "%s" must implement interface "%s".', $id, DataTypeInterface::class)
                );
            }

            foreach ($tags as $attributes) {
                if (false === isset($attributes['root_name'])) {
                    throw new \InvalidArgumentException(\sprintf('Service "%s" must have a root_name.', $id));
                }

                $definition->addMethodCall('add', [new Reference($id), $attributes['root_name']]);
            }
        }
    }
}
