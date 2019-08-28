<?php

namespace ZF3Belcebur\OAuth2SocialClient\Factory\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF3Belcebur\OAuth2SocialClient\Controller\Plugin\OAuth2SocialPlugin;
use ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService;

class OAuth2SocialPluginFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface|PluginManager $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return OAuth2SocialPlugin
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var PluginManager $pluginManager */
        $pluginManager = $container->get(PluginManager::class);
        return new OAuth2SocialPlugin(
            $container->get(OAuth2SocialService::class),
            $container->get(MvcTranslator::class),
            $pluginManager->get(Params::class),
            $pluginManager->get(Redirect::class),
            $pluginManager->has(FlashMessenger::class) ? $pluginManager->get(FlashMessenger::class) : null
        );
    }

}
