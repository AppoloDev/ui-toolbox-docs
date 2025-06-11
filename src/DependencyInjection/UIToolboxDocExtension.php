<?php

namespace AppoloDev\UIToolboxDocBundle\DependencyInjection;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class UIToolboxDocExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../../assets' => 'ui-toolbox-docs',
                    ],
                ],
            ]);
        }


    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!\interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');

        if (
            \is_array($bundlesMetadata)
            && isset($bundlesMetadata['FrameworkBundle'])
        ) {
            $frameworkBundleMetadata = $bundlesMetadata['FrameworkBundle'];

            if (\is_array($frameworkBundleMetadata) && isset($frameworkBundleMetadata['path']) && \is_string($frameworkBundleMetadata['path'])) {
                return \is_file($frameworkBundleMetadata['path'].'/Resources/config/asset_mapper.php');
            }
        }

        return false;
    }
}
