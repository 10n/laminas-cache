<?php

namespace Laminas\Cache;

use Laminas\Cache\Command\DeprecatedStorageFactoryConfigurationCheckCommand;
use Laminas\Cache\Command\DeprecatedStorageFactoryConfigurationCheckCommandFactory;
use Laminas\Cache\Service\StorageAdapterFactory;
use Laminas\Cache\Service\StorageAdapterFactoryFactory;
use Laminas\Cache\Service\StoragePluginFactory;
use Laminas\Cache\Service\StoragePluginFactoryFactory;

class ConfigProvider
{
    /**
     * Return default configuration for laminas-cache.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'laminas-cli'  => $this->getCliConfig(),
        ];
    }

    /**
     * Return default service mappings for laminas-cache.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'abstract_factories' => [
                Service\StorageCacheAbstractServiceFactory::class,
            ],
            'factories'          => [
                Storage\AdapterPluginManager::class => Service\StorageAdapterPluginManagerFactory::class,
                Storage\PluginManager::class        => Service\StoragePluginManagerFactory::class,
                DeprecatedStorageFactoryConfigurationCheckCommand::class
                    => DeprecatedStorageFactoryConfigurationCheckCommandFactory::class,
                StoragePluginFactory::class  => StoragePluginFactoryFactory::class,
                StorageAdapterFactory::class => StorageAdapterFactoryFactory::class,
            ],
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function getCliConfig(): array
    {
        return [
            'commands' => [
                DeprecatedStorageFactoryConfigurationCheckCommand::NAME
                    => DeprecatedStorageFactoryConfigurationCheckCommand::class,
            ],
        ];
    }
}
