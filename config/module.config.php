<?php

namespace ZF3Belcebur\OAuth2SocialClient;


use League\OAuth2\Client\Provider\Facebook;

return [
    'controller_plugins' => [

    ],
    __NAMESPACE__ => [
        'facebook' => [
            'factory-data' => [
                'class' => Facebook::class, // se others in https://github.com/thephpleague/oauth2-client/blob/master/docs/providers/thirdparty.md
                // redirectUri -> Url Option
                'callbackRoute' => 'https://www.google.es/callback',
            ],
            'provider-data' => [
                // See provider info in  https://github.com/thephpleague/oauth2-client
                'clientId' => 'xxx-your-client-id-xxx',
                'clientSecret' => 'xxx-your-client-secret-xxx',
                'graphApiVersion' => 'v3.0',
            ],
            'authorization-url-params' => [
                // Se
                'approval_prompt' => 'force', //forzar pantalla
                'scope' => [
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
    ],
];
