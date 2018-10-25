<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

declare(strict_types=1);

namespace Kajona\Oauth2\System;

use GuzzleHttp\Client;
use Kajona\System\System\Config;
use Kajona\System\System\Lifecycle\ServiceLifeCycleFactory;
use Kajona\System\System\Session;
use Kajona\System\System\Usersources\UsersourcesUserKajona;
use Kajona\System\System\UserUser;
use Lcobucci\JWT\Parser;

/**
 * ProviderManager
 *
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class ProviderManager
{
    const CLAIM_USERNAME = "username";
    const CLAIM_EMAIL = "email";
    const CLAIM_FIRSTNAME = "firstname";
    const CLAIM_LASTNAME = "lastname";

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var ServiceLifeCycleFactory
     */
    private $lifeCycleFactory;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Client $httpClient, ServiceLifeCycleFactory $lifeCycleFactory)
    {
        $this->httpClient = $httpClient;
        $this->lifeCycleFactory = $lifeCycleFactory;
        $this->config = Config::getInstance("module_oauth2");
    }

    /**
     * @param Provider $provider
     * @see https://tools.ietf.org/html/rfc6749#section-4.1.1
     */
    public function buildAuthorizationUrl(Provider $provider)
    {
        $params = [
            "response_type" => "code",
            "client_id" => $provider->getClientId(),
            "resource" => $provider->getResource(),
            "redirect_uri" => $provider->getRedirectUri(),
        ];

        return $provider->getAuthorizationUrl() . "?" . http_build_query($params, "", "&");
    }

    /**
     * Gets called after the user has successful authenticated at the remote provider. This method exchanges the code
     * for an access token and creates a new user (if necessary), then it creates a new session for the user
     *
     * @param Provider $provider
     * @param string $code
     */
    public function handleCallback(Provider $provider, $code)
    {
        $accessToken = $this->exchangeAccessToken($provider, $code);
        $certFile = $provider->getCertFile();

        if (!empty($certFile) && is_file($certFile)) {
            // @TODO optional we can verify the token
        }

        $token = (new Parser())->parse((string) $accessToken);

        $claimMapping = $this->config->getConfig("claim_mapping");
        if (empty($claimMapping) || !is_array($claimMapping)) {
            throw new \RuntimeException("Claim mapping not available");
        }

        $userName = $token->getClaim($claimMapping[self::CLAIM_USERNAME] ?? "");
        $email = $token->getClaim($claimMapping[self::CLAIM_EMAIL] ?? "");
        $firstName = $token->getClaim($claimMapping[self::CLAIM_FIRSTNAME] ?? "");
        $lastName = $token->getClaim($claimMapping[self::CLAIM_LASTNAME] ?? "");

        if (empty($userName)) {
            throw new \RuntimeException("No user name provided");
        }

        $userName = $this->normalizeName($userName);

        $user = $this->createOrGetUser($userName, $email, $firstName, $lastName);

        if ($user instanceof UserUser) {
            Session::getInstance()->loginUser($user);
        } else {
            throw new \RuntimeException("Could not find user");
        }
    }

    /**
     * Exchanges the obtained code for an access token at the token url
     *
     * @param Provider $provider
     * @param string $code
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see https://tools.ietf.org/html/rfc6749#section-4.1.3
     */
    public function exchangeAccessToken(Provider $provider, $code)
    {
        $response = $this->httpClient->request("POST", $provider->getTokenUrl(), [
            "form_params" => [
                "grant_type" => "authorization_code",
                "client_id" => $provider->getClientId(),
                "redirect_uri" => $provider->getRedirectUri(),
                "code" => $code,
            ],
            "verify" => false, // only for testing
        ]);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException("Invalid response code");
        }

        $data = (string) $response->getBody();
        $token = \GuzzleHttp\json_decode($data);

        $accessToken = $token->access_token ?? null;

        if (!empty($accessToken)) {
            return $accessToken;
        } else {
            throw new \RuntimeException("Received no token");
        }
    }

    /**
     * Returns a provider by id
     *
     * @param string $providerId
     * @return Provider
     */
    public function getProviderById($providerId)
    {
        $providers = $this->config->getConfig("providers");

        if (is_array($providers) && isset($providers[$providerId])) {
            return $this->newProvider($providerId, $providers[$providerId]);
        } else {
            throw new \InvalidArgumentException("Invalid provider id");
        }
    }

    /**
     * Returns all available providers
     *
     * @return Provider[]
     */
    public function getAvailableProviders()
    {
        $providers = $this->config->getConfig("providers");
        $result = [];

        if (is_array($providers)) {
            foreach ($providers as $index => $row) {
                $result[] = $this->newProvider($index, $row);
            }
        }

        return $result;
    }

    /**
     * @return Provider
     */
    public function getDefaultProvider()
    {
        return $this->getProviderById(0);
    }

    /**
     * @param string $index
     * @param array $row
     * @return Provider
     */
    private function newProvider($index, array $row)
    {
        $provider = new Provider();
        $provider->setId($index);
        $provider->setName($row["name"]);
        $provider->setClientId($row["client_id"]);
        $provider->setResource($row["resource"]);
        $provider->setRedirectUri($row["redirect_uri"]);
        $provider->setAuthorizationUrl($row["authorization_url"]);
        $provider->setTokenUrl($row["token_url"]);

        return $provider;
    }

    /**
     * @param string $userName
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @return UserUser|null
     * @throws \Kajona\System\System\Lifecycle\ServiceLifeCycleUpdateException
     */
    private function createOrGetUser($userName, $email, $firstName, $lastName)
    {
        $users = UserUser::getAllUsersByName($userName);
        if (empty($users)) {
            /** @var \Kajona\System\System\Usersources\UsersourcesUserKajona $sourceUser */
            $sourceUser = new UsersourcesUserKajona();
            $sourceUser->setStrEmail($email);
            $sourceUser->setStrForename($firstName);
            $sourceUser->setStrName($lastName);
            $sourceUser->setStrPass(generateSystemid());
            $this->lifeCycleFactory->factory(get_class($sourceUser))->update($sourceUser);

            $user = new UserUser();
            $user->setStrUsername($userName);
            $user->setObjSourceUser($sourceUser);
            $this->lifeCycleFactory->factory(get_class($user))->update($user);

            return $user;
        } else {
            return $users[0] ?? null;
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private function normalizeName($name)
    {
        // in case the name contains also the domain, remove the domain name
        if (strpos("\\", $name) !== false) {
            return substr(strstr($name, "\\"), 1);
        }

        return $name;
    }
}
