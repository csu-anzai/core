<?php
/*"******************************************************************************************************
 *   (c) ARTEMEON Management Partner GmbH
 *       Published under the GNU LGPL v2.1
 ********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\ObjectBuilder;
use Pimple\Container;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Exception\StatusCodeException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\Request;
use PSX\Uri\Uri;
use Slim\App;
use Slim\Container as SlimContainer;
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;

/**
 * AppBuilder
 *
 * @author christoph.kappestein@gmail.com
 * @since 7.1
 */
class AppBuilder
{
    /**
     * @var EndpointScanner
     */
    private $endpointScanner;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param EndpointScanner $endpointScanner
     * @param Container $container
     */
    public function __construct(EndpointScanner $endpointScanner, Container $container)
    {
        $this->endpointScanner = $endpointScanner;
        $this->container = $container;
    }

    /**
     * Creates a new app, attaches all available routes and executes the app
     *
     * @throws \Kajona\System\System\Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function run()
    {
        $app = $this->newApp();
        $container = $this->container;
        $routes = $this->endpointScanner->getEndpoints();

        foreach ($routes as $route) {
            $app->map($route["httpMethod"], $route["path"], function(SlimRequest $request, SlimResponse $response, array $args) use ($route, $container){
                /** @var ObjectBuilder $objectBuilder */
                $objectBuilder = $container->offsetGet(\Kajona\System\System\ServiceProvider::STR_OBJECT_BUILDER);
                $instance = $objectBuilder->factory($route["class"]);

                try {
                    $auth = $route["authorization"] ?? null;
                    if (!empty($auth)) {
                        /** @var AuthorizationInterface $authorization */
                        $authorization = $container->offsetGet("api_authorization_" . $auth);

                        if (!$authorization->authorize($request->getHeaderLine("Authorization"))) {
                            throw new UnauthorizedException("Request not authorized", "Bearer");
                        }
                    }

                    $body = $request->getParsedBody();
                    $httpContext = new HttpContext(new Request(new Uri($request->getUri()->__toString()), $request->getMethod(), $request->getHeaders()), $args);

                    if (in_array($request->getMethod(), ["GET", "HEAD"])) {
                        $data = call_user_func_array([$instance, $route["methodName"]], [$httpContext]);
                    } else {
                        $data = call_user_func_array([$instance, $route["methodName"]], [$body, $httpContext]);
                    }

                    $response = $response->withHeader("Content-Type", "application/json")
                        ->write(json_encode($data, JSON_PRETTY_PRINT));
                } catch (StatusCodeException $e) {
                    $response = $response->withStatus($e->getStatusCode())
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode(["error" => $e->getMessage()]));
                } catch (\Throwable $e) {
                    $response = $response->withStatus(500)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode(["error" => $e->getMessage()]));
                }

                // add CORS header
                $response = $response
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                    ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH');

                return $response;
            });
        }

        $app->run();
    }

    /**
     * @return App
     */
    private function newApp()
    {
        $container = new SlimContainer();
        $container['notFoundHandler'] = function ($c) {
            return function (SlimRequest $request, SlimResponse $response) use ($c) {
                $data = [
                    "error" => "Endpoint not found"
                ];

                return $response->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data, JSON_PRETTY_PRINT));
            };
        };

        $container['notAllowedHandler'] = function ($c) {
            return function (SlimRequest $request, SlimResponse $response, $methods) use ($c) {
                $data = [
                    "error" => "Method not allowed"
                ];

                return $response->withStatus(405)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-type', 'text/html')
                    ->write(json_encode($data, JSON_PRETTY_PRINT));
            };
        };

        return new App($container);
    }
}
