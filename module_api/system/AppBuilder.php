<?php
/*"******************************************************************************************************
*   (c) 2015-2016 by Kajona, www.kajona.de                                                         *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\ObjectBuilder;
use Pimple\Container;
use PSX\Http\Environment\HttpContext;
use PSX\Http\Request;
use PSX\Uri\Uri;
use Slim\App;
use Slim\Http\Request as HttpRequest;
use Slim\Http\Response as HttpResponse;

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
            $app->map($route["httpMethod"], $route["path"], function(HttpRequest $request, HttpResponse $response, array $args) use ($route, $container){
                /** @var ObjectBuilder $objectBuilder */
                $objectBuilder = $container->offsetGet(\Kajona\System\System\ServiceProvider::STR_OBJECT_BUILDER);
                $instance = $objectBuilder->factory($route["class"]);

                try {
                    $body = $request->getParsedBody();
                    $httpContext = new HttpContext(new Request(new Uri($request->getUri()->__toString()), $request->getMethod(), $request->getHeaders()), $args);

                    if (in_array($request->getMethod(), ["GET", "HEAD"])) {
                        $data = call_user_func_array([$instance, $route["methodName"]], [$httpContext]);
                    } else {
                        $data = call_user_func_array([$instance, $route["methodName"]], [$body, $httpContext]);
                    }

                    $response = $response->withHeader("Content-Type", "application/json")
                        ->write(json_encode($data, JSON_PRETTY_PRINT));
                } catch (\Throwable $e) {
                    $response = $response->withStatus(404)
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
        $container = new \Slim\Container();
        $container['notFoundHandler'] = function ($c) {
            return function (HttpRequest $request, HttpResponse $response) use ($c) {
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
