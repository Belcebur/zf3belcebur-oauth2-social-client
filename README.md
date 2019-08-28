# zf3belcebur-oauth2-social-client
ZF3 extends module from thephpleague/oauth2-client

## See
- [https://packagist.org/explore/?query=zf3belcebur](https://packagist.org/explore/?query=zf3belcebur)
- [https://oauth2-client.thephpleague.com/](https://oauth2-client.thephpleague.com/)
- [https://oauth2-client.thephpleague.com/providers/league/](https://oauth2-client.thephpleague.com/providers/league/)
- [https://oauth2-client.thephpleague.com/providers/thirdparty/](https://oauth2-client.thephpleague.com/providers/thirdparty/)

## Installation

Installation of this module uses composer. For composer documentation, please refer to
[getcomposer.org](http://getcomposer.org/).

```sh
composer require zf3belcebur/oauth2-social-client
```

Then add `ZF3Belcebur\OAuth2SocialClient` to your `config/application.config.php` and copy `config/zf3belcebur-oauth2-social.global.php.dist` to your autoload config folder and configure it with your providers.



### Callback route format Url or Zend Route
```php
<?php
[
    'CUSTOM_PROVIDER_KEY'  => [
        'factory-data'             => [
            'class'         => \League\OAuth2\Client\Provider\Facebook::class, // se others in https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md
            // redirectUri -> Url Option
            'callbackRoute' => 'https://www.google.es/callback',
            // redirectUri -> Zend Route Option
            'callbackRoute' => [
                'name'   => 'belcebur-social',
                'params' => [ 'action' => 'facebook' ],
            ],
        ],
    ],
]
```
## How to use?

### Get Controller Plugin and Get Callback Response from provider
```php
<?php

namespace Application\Controller;

use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZF3Belcebur\OAuth2SocialClient\Controller\Plugin\OAuth2SocialPlugin;

/**
 * Class IndexController
 * @package Application\Controller
 * @method OAuth2SocialPlugin oAuth2Social()
 */
class IndexController extends AbstractActionController
{
    public function indexAction(): ViewModel
    {
        $oAuth2Social = $this->oAuth2Social(); // return \ZF3Belcebur\OAuth2SocialClient\Controller\Plugin\OAuth2SocialPlugin
        $oAuth2Social->getOAuth2Social()->getAll(); // Return array with all providers
        $oAuth2Social->getOAuth2Social()->getProviderByName('CUSTOM_PROVIDER_KEY'); // Return provider;
        $oAuth2Social->getOAuth2Social()->getAuthorizationUrl('CUSTOM_PROVIDER_KEY', ['extraParam' => 'extraValue']); // Return authorization url from a provider

        return new ViewModel();
    }

    public function googleCallbackAction(): \Zend\Http\Response
    {
        $state = json_decode($this->params()->fromQuery('state', '{}'), true);
        $oAuth2Social = $this->oAuth2Social(); // return \ZF3Belcebur\OAuth2SocialClient\Controller\Plugin\OAuth2SocialPlugin
        return $oAuth2Social->getProviderResponse($state['provider'] ?? 'google', static function (GoogleUser $googleUser, AccessToken $token) use ($state) {
            \var_dump($googleUser);
            \var_dump($token);
            \var_dump($state);
            return $this->redirect()->toUrl($state['currentUrl']);
        }, $oAuth2Social->defaultCallbackError);
    }
}

```

### View Helper
```php
<?php

/** @var \ZF3Belcebur\OAuth2SocialClient\View\Helper\OAuth2SocialViewHelper $oAuth2Social */
$oAuth2Social = $this->oAuth2Social();
 
//return \Zend\Router\Http\RouteMatch
$oAuth2Social->getRouteMatch();
 
// return \Zend\Http\PhpEnvironment\Request
$oAuth2Social->getRequest();
 
// return \Zend\Router\RouteStackInterface
$oAuth2Social->getRouter();
 
// return ['CUSTOM_PROVIDER_KEY'=>'CUSTOM_PROVIDER_KEY_URL','CUSTOM_PROVIDER_KEY2'=>'CUSTOM_PROVIDER_KEY2_URL']
$oAuth2Social->getAllAuthorizationUrl(['CUSTOM_PROVIDER_KEY'=>['param1'=>'$value1'],'CUSTOM_PROVIDER_KEY_2'=>['param1'=>'$value1','param2'=>'$value2']]);
 
// return 'CUSTOM_PROVIDER_KEY_URL'
$oAuth2Social->getAuthorizationUrlByName('CUSTOM_PROVIDER_KEY');
 
// return \ZF3Belcebur\OAuth2SocialClient\Service\OAuth2SocialService
$oAuth2Social->getOAuth2SocialService(); 

?>

```
## Config File

###`config/zf3belcebur-oauth2-social.global.php.dist`
```php
<?php
[
    __NAMESPACE__ => [
        'facebook'  => [
            'factory-data'             => [
                'class'         => \League\OAuth2\Client\Provider\Facebook::class, // se others in https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md
                // redirectUri -> Url Option
                'callbackRoute' => 'https://www.google.es/callback',
            ],
            'provider-data'            => [
                // See provider info in  https://github.com/thephpleague/oauth2-client
                'clientId'        => 'xxx-your-client-id-xxx',
                'clientSecret'    => 'xxx-your-client-secret-xxx',
                'graphApiVersion' => 'v3.0',
                // other provider data
            ],
            'authorization-url-params' => [
                'approval_prompt' => 'force', //forzar pantalla
                'scope'           => [
                    'public_profile',
                    'email',
                    'user_birthday',
                    'user_gender',
                    'user_link',
                    'user_location',
                    'user_hometown',
                ],
            ],
        ],
        'google'    => [
            'factory-data'  => [
                'class'         => \League\OAuth2\Client\Provider\Google::class,
                'callbackRoute' => [
                    'name'   => 'belcebur-social',
                    'params' => [ 'action' => 'google' ],
                ],
            ],
            'provider-data' => [
                'clientId'        => 'xxx-your-client-id-xxx',
                'clientSecret'    => 'xxx-your-client-secret-xxx',
            ],
        ],
        'intranet'  => [
            'factory-data'             => [
                'class'         => \League\OAuth2\Client\Provider\Google::class,
                'callbackRoute' => [
                    'name'   => 'belcebur-social',
                    'params' => [ 'action' => 'intranet' ],
                ],
            ],
            'provider-data'            => [
                'clientId'        => 'xxx-your-client-id-xxx',
                'clientSecret'    => 'xxx-your-client-secret-xxx',
                'hostedDomain' => 'example.com',
            ],
            'authorization-url-params' => [
                //'approval_prompt' => 'force', //forzar pantalla
                'scope' => [
                    'email',
                    'openid',
                    'profile',
                    'https://mail.google.com/',
                ],
            ],
        ],
        'linkedin'  => [
            'factory-data'  => [
                'class'         => \League\OAuth2\Client\Provider\LinkedIn::class,
                'callbackRoute' => [
                    'name'   => 'belcebur-social',
                    'params' => [ 'action' => 'linkedin' ],
                ],
            ],
            'provider-data' => [
                'clientId'        => 'xxx-your-client-id-xxx',
                'clientSecret'    => 'xxx-your-client-secret-xxx',
            ],

        ],
        'microsoft' => [
            'factory-data'             => [
                'class'         => \Stevenmaguire\OAuth2\Client\Provider\Microsoft::class,
                'callbackRoute' => [
                    'name'   => 'belcebur-social',
                    'params' => [ 'action' => 'microsoft' ],
                ],
            ],
            'provider-data'            => [
                'clientId'        => 'xxx-your-client-id-xxx',
                'clientSecret'    => 'xxx-your-client-secret-xxx',
            ],
            'authorization-url-params' => [
                'scope' => [
                    'wl.basic',
                    'wl.signin',
                    'wl.emails',
                ],
            ],
        ],
    ],
];
```
    
