<?php

namespace ZF3Belcebur\OAuth2SocialClient\Controller\Plugin;

use Closure;
use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use RuntimeException;
use Zend\Http\Response;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Mvc\Controller\Plugin\Params;
use Zend\Mvc\Controller\Plugin\Redirect;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;
use ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService;


class OAuth2SocialPlugin extends AbstractPlugin
{

    /** @var Closure */
    public $defaultCallbackError;
    /** @var MvcTranslator */
    protected $mvcTranslator;
    /** @var OAuth2SocialService */
    protected $oAuth2Social;
    /**
     * @var Params
     */
    private $params;
    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * BaseOAuthSocialController constructor.
     * @param OAuth2SocialService $oAuth2Social
     * @param MvcTranslator $mvcTranslator
     * @param Params $params
     * @param Redirect $redirect
     * @param FlashMessenger|null $flashMessenger
     */
    public function __construct(OAuth2SocialService $oAuth2Social, MvcTranslator $mvcTranslator, Params $params, Redirect $redirect, ?FlashMessenger $flashMessenger)
    {
        $this->oAuth2Social = $oAuth2Social;
        $this->mvcTranslator = $mvcTranslator;
        $this->params = $params;
        $this->redirect = $redirect;

        $this->defaultCallbackError = static function (string $errorMessage, ?Exception $exception) use ($flashMessenger): Response {
            $state = json_decode($this->params->fromQuery('state', '{}'), true);
            $lastUrl = $state['currentUrl'] ?? $_SERVER['SERVER_ADDR'];

            if ($flashMessenger) {
                $flashMessenger->addErrorMessage($errorMessage);
            }
            return $this->redirect->toUrl($lastUrl);
        };
    }

    public function getProviderResponse(string $nameType = null, Closure $callbackSuccess = null, Closure $callbackError = null): Response
    {

        $state = json_decode($this->params->fromQuery('state', '{}'), true);
        $provider = $this->oAuth2Social->getProviderByName($nameType ?? $state['provider']);
        if (!($provider instanceof AbstractProvider)) {
            throw new RuntimeException(sprintf($this->mvcTranslator->translate('Provider %s is not instance of %s'), get_class($provider), AbstractProvider::class));
        }

        $lastUrl = $state['currentUrl'] ?? '';

        if (!$this->params->fromQuery('code')) {
            return $this->redirect->toUrl($provider->getAuthorizationUrl());
        }

        $errorMessage = $this->params->fromQuery('error');
        $exception = null;
        try {
            /** @var AccessToken $token */
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->params->fromQuery('code'),
            ]);

            // We got an access token, let's now get the owner details
            $ownerDetails = $provider->getResourceOwner($token);

            if ($callbackSuccess) {
                return $callbackSuccess($ownerDetails, $token);
            }
        } catch (Exception $exception) {
            $errorMessage = $exception->getMessage();
        }

        if ($errorMessage && $callbackError) {
            return $callbackError($errorMessage, $exception);
        }

        return $this->redirect->toUrl($lastUrl);

    }

    /**
     * @return OAuth2SocialService
     */
    public function getOAuth2Social(): OAuth2SocialService
    {
        return $this->oAuth2Social;
    }

}
