<?php
/*"******************************************************************************************************
*   (c) 2015-2016 by Kajona, www.kajona.de                                                         *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\ObjectBuilder;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Request;
use PSX\Uri\Uri;
use Slim\App;

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
     * Attaches all available routes to the slim app
     *
     * @return App
     * @throws \Kajona\System\System\Exception
     */
    public function build()
    {
        $app = $this->newApp();
        $container = $this->container;
        $routes = $this->endpointScanner->getEndpoints();

        foreach ($routes as $route) {
            $app->map($route["httpMethod"], $route["path"], function(ServerRequestInterface $request, ResponseInterface $response, array $args) use ($route, $container){
                /** @var ObjectBuilder $objectBuilder */
                $objectBuilder = $container->offsetGet(\Kajona\System\System\ServiceProvider::STR_OBJECT_BUILDER);
                $instance = $objectBuilder->factory($route["class"]);

                try {
                    $body = $request->getParsedBody();
                    $httpContext = new HttpContext(new Request(new Uri($request->getUri()->__toString()), $request->getMethod(), $request->getHeaders()), $args);

                    $data = call_user_func_array([$instance, $route["methodName"]], [$httpContext, $body]);

                    $response = $response->withHeader("Content-Type", "application/json");
                    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
                } catch (\Throwable $e) {
                    $response = $response->withStatus(404);
                    $response = $response->withHeader("Content-Type", "application/json");
                    $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
                }

                return $response;
            });
        }

        return $app;
    }

    /**
     * @return App
     */
    private function newApp()
    {
        $container = new \Slim\Container();
        $container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                $data = [
                    "error" => "Endpoint not found"
                ];

                return $response->withStatus(404)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data, JSON_PRETTY_PRINT));
            };
        };

        return new App($container);
    }
}
