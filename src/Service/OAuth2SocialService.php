<?php
/**
 * Created by PhpStorm.
 * User: dgarcia
 * Date: 14/11/2018
 * Time: 16:16
 */

namespace ZF3Belcebur\OAuth2SocialClient\Service;

use League\OAuth2\Client\Provider\AbstractProvider;
use Zend\Router\Http\TranslatorAwareTreeRouteStack;
use Zend\Router\Http\TreeRouteStack;
use Zend\Router\RouteStackInterface;
use ZF3Belcebur\OAuth2SocialClient\Module;
use function array_key_exists;
use function array_merge_recursive;
use function is_array;

class OAuth2SocialService
{

    /**
     * @var array
     */
    private $all;

    /**
     * @var array
     */
    private $config;

    /** @var RouteStackInterface */
    private $router;

    /**
     * OAuth2SocialFactoryService constructor.
     *
     * @param TranslatorAwareTreeRouteStack|TreeRouteStack|RouteStackInterface $router
     * @param array $config
     */
    public function __construct(RouteStackInterface $router, array $config)
    {
        $this->router = $router;
        $this->config = (array)($config[Module::CONFIG_KEY] ?? []);

        foreach ($this->config as $key => $data) {
            $factoryData = $data['factory-data'] ?? [];

            if (array_key_exists('class', $factoryData)) {
                $providerData = $data['provider-data'] ?? [];

                if (is_array($factoryData['callbackRoute'])) {
                    $routeParams = $factoryData['callbackRoute']['params'];
                    $routeName = $factoryData['callbackRoute']['name'];


                    $providerData['redirectUri'] = $router->assemble($routeParams, ['name' => $routeName, 'force_canonical' => true]);
                }

                $provider = new $factoryData['class']($providerData);
                $this->all[$key] = $provider;
            }
        }
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->all;
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    public function getAuthorizationUrl(string $name, array $params = []): string
    {
        $params['state'] = json_encode(array_merge([
            'currentUrl' => $this->router->getRequestUri()->toString(),
            'provider' => $name,
        ], $params));

        $provider = $this->getProviderByName($name);
        if ($provider) {
            $defaultParams = (array)($this->config[$name]['authorization-url-params'] ?? []);
            $options = array_merge_recursive($defaultParams, $params);
            return $provider->getAuthorizationUrl($options);
        }

        return '!#';

    }

    /**
     * @param string|null $name
     *
     * @return AbstractProvider|null
     */
    public function getProviderByName(string $name): ?AbstractProvider
    {
        return $this->all[$name] ?? null;
    }

}
