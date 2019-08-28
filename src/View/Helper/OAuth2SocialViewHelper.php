<?php
/**
 * Created by PhpStorm.
 * User: pgarcia
 * Date: 30/04/2018
 * Time: 10:53
 */

namespace ZF3Belcebur\OAuth2SocialClient\View\Helper;


use Zend\Http\PhpEnvironment\Request;
use Zend\Router\Http\RouteMatch;
use Zend\Router\RouteStackInterface;
use Zend\View\Helper\AbstractHelper;
use ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService;

class OAuth2SocialViewHelper extends AbstractHelper
{

    /**
     * @var OAuth2SocialService
     */
    private $oAuth2SocialService;

    /**
     * @var RouteStackInterface
     */
    private $router;

    /**
     * @var Request
     */
    private $request;

    /**
     * OAuthSocialHelper constructor.
     *
     * @param OAuth2SocialService $oAuth2SocialService
     * @param RouteStackInterface $router
     * @param Request $request
     */
    public function __construct(OAuth2SocialService $oAuth2SocialService, RouteStackInterface $router, Request $request)
    {
        $this->oAuth2SocialService = $oAuth2SocialService;
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @param array $newParams ['CUSTOM_PROVIDER_KEY'=>['param1'=>'$value1'],'CUSTOM_PROVIDER_KEY_2'=>['param1'=>'$value1','param2'=>'$value2']]
     * @return array
     *
     */
    public function getAllAuthorizationUrl(array $newParams = []): array
    {
        $providersUrls = [];

        foreach ($this->getOAuth2SocialService()->getAll() as $name => $provider) {
            $providersUrls[$name] = $this->getAuthorizationUrlByName($name, $newParams[$name] ?? []);
        }

        return $providersUrls;
    }

    /**
     * @return OAuth2SocialService
     */
    public function getOAuth2SocialService(): OAuth2SocialService
    {
        return $this->oAuth2SocialService;
    }

    /**
     * @param string $name
     * @param array $newParams
     * @return string
     */
    public function getAuthorizationUrlByName(string $name, array $newParams = []): string
    {
        return $this->getOAuth2SocialService()->getAuthorizationUrl($name, $newParams);
    }

    /**
     * @param Request|null $request
     *
     * @return RouteMatch|null
     */
    public function getRouteMatch(Request $request = null): ?RouteMatch
    {
        if (!$request) {
            $request = $this->getRequest();
        }

        /** @var RouteMatch $routeMatch */
        $routeMatch = $this->getRouter()->match($request);
        return $routeMatch;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return RouteStackInterface
     */
    public function getRouter(): RouteStackInterface
    {
        return $this->router;
    }

}
