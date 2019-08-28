<?php
/**
 * Created by PhpStorm.
 * User: dgarcia
 * Date: 4/06/14
 * Time: 16:12
 */

namespace ZF3Belcebur\OAuth2SocialClient\Factory\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService;

class OAuth2SocialFactoryService implements FactoryInterface
{

    /**
     * Create an \ZF3Belcebur\OAuth2SocialClient\Factory\OAuth2SocialService
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     *
     * @return OAuth2SocialService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OAuth2SocialService(
            $container->get('Router'),
            $container->get('Config')
        );
    }
}
