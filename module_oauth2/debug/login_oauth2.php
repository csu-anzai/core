<?php

/** @var \Kajona\Oauth2\System\ProviderManager $providerManager */
$providerManager = \Kajona\System\System\Carrier::getInstance()->getContainer()->offsetGet(\Kajona\Oauth2\System\ServiceProvider::STR_PROVIDER_MANAGER);
$providers = $providerManager->getAvailableProviders();

if (count($providers) > 0) {
    foreach ($providers as $provider) {
        $title = "Login with " . $provider->getName();
        $url = $providerManager->buildAuthorizationUrl($provider);

        echo "<a href='{$url}'>{$title}</a>\n";
        echo "<hr>";
    }
}
