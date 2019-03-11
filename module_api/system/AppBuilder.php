<?php
/*"******************************************************************************************************
*   (c) 2015-2016 by Kajona, www.kajona.de                                                         *
*       Published under the GNU LGPL v2.1, see /system/licence_lgpl.txt                                 *
********************************************************************************************************/

namespace Kajona\Api\System;

use Kajona\System\System\CacheManager;
use Kajona\System\System\Classloader;
use Kajona\System\System\ObjectBuilder;
use Kajona\System\System\Reflection;
use Kajona\System\System\Resourceloader;
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
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param CacheManager $cacheManager
     * @param Container $container
     */
    public function __construct(CacheManager $cacheManager, Container $container)
    {
        $this->cacheManager = $cacheManager;
        $this->container = $container;
    }

    /**
     * Attaches all available routes to the slim app
     *
     * @param App $app
     * @throws \Kajona\System\System\Exception
     */
    public function build(App $app)
    {
        $container = $this->container;
        $routes = $this->fetchRoutes();
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
    }

    /**
     * Parses all API controller classes for specific annotations and builds an array containing all available routes
     *
     * @return array
     * @throws \Kajona\System\System\Exception
     */
    private function fetchRoutes()
    {
        $routes = $this->cacheManager->getValue("api_routes");
        if (!empty($routes)) {
            return $routes;
        }

        $routes = [];
        $classes = $this->getAllApiController();
        foreach ($classes as $class) {
            $reflection = new Reflection($class);
            $methods = $reflection->getMethodsWithAnnotation("@api");

            foreach ($methods as $methodName => $values) {
                $method = array_map("trim", explode(",", $reflection->getMethodAnnotationValue($methodName, "@method")));
                $path = $reflection->getMethodAnnotationValue($methodName, "@path");

                $routes[] = [
                    "httpMethod" => $method,
                    "path" => $path,
                    "class" => $class,
                    "methodName" => $methodName,
                ];
            }
        }

        $this->cacheManager->addValue("api_routes", $routes);

        return $routes;
    }

    /**
     * Returns all available API controller classes
     *
     * @return array
     */
    private function getAllApiController()
    {
        $filter = function (&$strOneFile, $strPath) {
            $instance = Classloader::getInstance()->getInstanceFromFilename($strPath, ApiControllerInterface::class);
            if ($instance instanceof ApiControllerInterface) {
                $strOneFile = get_class($instance);
            } else {
                $strOneFile = null;
            }
        };

        $classes = Resourceloader::getInstance()->getFolderContent("/api", array(".php"), false, null, $filter);
        $classes = array_filter($classes);
        $classes = array_values($classes);

        return $classes;
    }
}
