<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Oauth2\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kajona\Oauth2\System\ProviderManager;
use Kajona\Oauth2\System\VoidLogger;
use Kajona\System\System\Carrier;
use Kajona\System\System\ServiceProvider;
use Kajona\System\System\Session;
use Kajona\System\System\UserUser;
use Kajona\System\Tests\Testbase;
use Lcobucci\JWT\Builder;

/**
 * @author christoph.kappestein@artemeon.de
 * @since 7.0
 */
class ProviderManagerTest extends Testbase
{
    public function testBuildAuthorizationUrl()
    {
        $httpClient = new Client();
        $lifeCycleFactory = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_LIFE_CYCLE_FACTORY);
        $session = Session::getInstance();
        $logger = new VoidLogger();
        $providersConfig = $this->getProvidersConfig();

        $providerManager = new ProviderManager($httpClient, $lifeCycleFactory, $session, $logger, $providersConfig);
        $provider = $providerManager->getDefaultProvider();

        $authorizationUrl = $providerManager->buildAuthorizationUrl($provider);

        $this->assertEquals("https://winsrv01.artemeon.de/adfs/oauth2/authorize?response_type=code&client_id=824e247e-9557-4e20-854a-83292f8dd84f&resource=https%3A%2F%2F192.168.60.209&redirect_uri=http%3A%2F%2F192.168.60.209%2Fagp%2Fauthorization%2Fcallback", $authorizationUrl);
    }

    public function testHandleCallback()
    {
        $token = (new Builder())->setIssuer('http://example.com')
            ->setAudience('http://example.org')
            ->setId('4f1g23a12aa', true)
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 3600)
            ->set('unique_name', 'AD\\cka')
            ->set('email', 'christoph.kappestein@artemeon.de')
            ->set('first_name', 'Christoph')
            ->set('last_name', 'Kappestein')
            ->getToken();

        $data = [
            "type" => "Bearer",
            "access_token" => $token->__toString(),
            "expires_in" => time() + 60,
        ];

        $mock = new MockHandler([
            new Response(200, ["Content-Type" => "application/json"], \json_encode($data)),
        ]);

        $handler = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handler]);

        $lifeCycleFactory = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_LIFE_CYCLE_FACTORY);
        $session = Session::getInstance();
        $providersConfig = $this->getProvidersConfig();

        $providerManager = $this->getMockBuilder(ProviderManager::class)
            ->setConstructorArgs([$httpClient, $lifeCycleFactory, $session, $providersConfig])
            ->setMethods(["createOrGetUser", "loginUser"])
            ->getMock();

        $providerManager->expects($this->once())
            ->method("createOrGetUser")
            ->will($this->returnCallback(function($userName, $email, $firstName, $lastName){
                $this->assertEquals("cka", $userName);
                $this->assertEquals("christoph.kappestein@artemeon.de", $email);
                $this->assertEquals("Christoph", $firstName);
                $this->assertEquals("Kappestein", $lastName);

                $user = new UserUser();
                $user->setStrUsername($userName);
                return $user;
            }));

        $providerManager->expects($this->once())
            ->method("loginUser")
            ->with($this->callback(function(UserUser $user){
                $this->assertEquals("cka", $user->getStrUsername());
                return true;
            }));

        $provider = $providerManager->getDefaultProvider();
        $providerManager->handleCallback($provider, "[code]");
    }

    public function testGetAvailableProviders()
    {
        $httpClient = new Client();
        $lifeCycleFactory = Carrier::getInstance()->getContainer()->offsetGet(ServiceProvider::STR_LIFE_CYCLE_FACTORY);
        $session = Session::getInstance();
        $providersConfig = $this->getProvidersConfig();

        $providerManager = new ProviderManager($httpClient, $lifeCycleFactory, $session, $providersConfig);
        $result = $providerManager->getAvailableProviders();

        $this->assertEquals(1, count($result));
    }

    private function getProvidersConfig()
    {
        $providersConfig = [];
        $providersConfig[0] = [
            "name" => "Winsrv01",
            "client_id" => "824e247e-9557-4e20-854a-83292f8dd84f", // the Id of the Client wanting an access token, as regiestered in the ClientId parameter when registering the Client in ADFS.
            "resource" => "https://192.168.60.209", // The resource server that the Client wants an access token to, as registered in the Identifier parameter of the Relying Party trust
            "redirect_uri" => "http://192.168.60.209/agp/authorization/callback", // The redirect uri that is associated with the Client. Must match the RedirectUri value associated with the Client in ADFS.
            "authorization_url" => "https://winsrv01.artemeon.de/adfs/oauth2/authorize",
            "token_url" => "https://winsrv01.artemeon.de/adfs/oauth2/token",
            "cert_file" => null,
            "claim_mapping" => [
                \Kajona\Oauth2\System\ProviderManager::CLAIM_USERNAME => "unique_name",
                \Kajona\Oauth2\System\ProviderManager::CLAIM_EMAIL => "email",
                \Kajona\Oauth2\System\ProviderManager::CLAIM_FIRSTNAME => "first_name",
                \Kajona\Oauth2\System\ProviderManager::CLAIM_LASTNAME => "last_name",
            ]
        ];

        return $providersConfig;
    }
}
