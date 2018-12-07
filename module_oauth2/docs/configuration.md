
# OAuth2 configuration

This document should help to setup a remote OAuth2 provider

## .htaccess

At first you need to add a new `.htaccess` rule to redirect requests for the path `/authorization/callback`
to the fitting module and action:

```
RewriteCond %{QUERY_STRING} code=(.+) [NC]
RewriteRule ^authorization/callback xml.php?admin=1&module=oauth2&action=callback&code=%1
```

## Provider

In most cases the remote provider needs to create an app, therefor he needs to enter the following
callback URL of the AGP: `https://[host]/authorization/callback`

## Config

If we have received the app credentials from the provider we can configure the OAuth2 provider on
the AGP side:

```
$config["providers"][0] = [
    "name" => "Winsrv01",
    "client_id" => "824e247e-9557-4e20-854a-83292f8dd84f", // the Id of the Client wanting an access token, as regiestered in the ClientId parameter when registering the Client in ADFS.
    "resource" => "https://192.168.60.209", // The resource server that the Client wants an access token to, as registered in the Identifier parameter of the Relying Party trust
    "redirect_uri" => "http://192.168.60.209/agp/authorization/callback", // The redirect uri that is associated with the Client. Must match the RedirectUri value associated with the Client in ADFS.
    "authorization_url" => "https://winsrv01.artemeon.de/adfs/oauth2/authorize",
    "token_url" => "https://winsrv01.artemeon.de/adfs/oauth2/token",
    "cert_file" => null,
    "claim_mapping" => [
        \Kajona\Oauth2\System\ProviderManager::CLAIM_USERNAME => "user_name",
        \Kajona\Oauth2\System\ProviderManager::CLAIM_EMAIL => "email",
        \Kajona\Oauth2\System\ProviderManager::CLAIM_FIRSTNAME => "first_name",
        \Kajona\Oauth2\System\ProviderManager::CLAIM_LASTNAME => "last_name",
    ]
];
```

## Claims

The claim mapping contains a simple map with names which are used in the JWT which we receive from
the provider. Through this information we can identify/create a new user. Which claims are available
in the token needs to be discussed with the remote provider.


