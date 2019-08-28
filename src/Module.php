<?php

namespace ZF3Belcebur\OAuth2SocialClient;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ServiceManager\Config;
use ZF3Belcebur\OAuth2SocialClient\Controller\Plugin\OAuth2SocialPlugin;
use ZF3Belcebur\OAuth2SocialClient\Factory\Controller\Plugin\OAuth2SocialPluginFactory;
use ZF3Belcebur\OAuth2SocialClient\Factory\Service\OAuth2SocialFactoryService as OAuth2SocialFactory;
use ZF3Belcebur\OAuth2SocialClient\Factory\View\Helper\OAuth2SocialFactoryViewHelper;
use ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService;
use ZF3Belcebur\OAuth2SocialClient\View\Helper\OAuth2SocialViewHelper;

class Module implements ConfigProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface, ControllerPluginProviderInterface
{
    public const CONFIG_KEY = __NAMESPACE__;

    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|Config
     */
    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                OAuth2SocialService::class => OAuth2SocialFactory::class,
            ],
        ];
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|Config
     */
    public function getViewHelperConfig(): array
    {
        return [
            'factories' => [
                OAuth2SocialViewHelper::class => OAuth2SocialFactoryViewHelper::class,
            ],
            'aliases' => [
                'oAuth2Social' => OAuth2SocialViewHelper::class,
            ],
        ];
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|Config
     */
    public function getControllerPluginConfig(): array
    {
        return [
            'aliases' => [
                'oAuth2Social' => OAuth2SocialPlugin::class,
            ],
            'factories' => [
                OAuth2SocialPlugin::class => OAuth2SocialPluginFactory::class,
            ],
        ];
    }
}
