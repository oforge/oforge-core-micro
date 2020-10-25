<?php

namespace Oforge\Engine\Core\Managers\Slim;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Forge\ForgeSlimApp;
use Oforge\Engine\Core\Middlewares\DebugModeMiddleware;
use Oforge\Engine\Core\Middlewares\SessionMiddleware;
use Oforge\Engine\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Core\Models\Plugin\Middleware;
use Oforge\Engine\Core\Services\EndpointService;
use Oforge\Engine\Core\Services\MiddlewareService;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteInterface;

/**
 * Class RouteManager
 *
 * @package Oforge\Engine\Core\Managers\Slim
 */
class SlimRouteManager {
    /** @var SlimRouteManager $instance */
    protected static $instance = null;

    /** @return SlimRouteManager */
    public static function getInstance() : SlimRouteManager {
        if (!isset(self::$instance)) {
            self::$instance = new SlimRouteManager();
        }

        return self::$instance;
    }

    /**
     * Make all routes, that come from the database, work
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function init() {
        /** @var ForgeSlimApp $forgeSlimApp */
        $forgeSlimApp = Oforge()->App();
        /** @var ContainerInterface $container */
        $container = $forgeSlimApp->getContainer();
        /** @var MiddlewareService $middlewareService */
        $middlewareService = Oforge()->Services()->get('middleware');
        /** @var EndpointService $endpointService */
        $endpointService = Oforge()->Services()->get('endpoint');
        /** @var Middleware[] $activeMiddlewares */
        $activeMiddlewares = $middlewareService->getActiveMiddlewares();
        /** @var Endpoint[] $endpoints */
        $endpoints = $endpointService->getActiveEndpoints();

        foreach ($endpoints as $endpoint) {
            $httpMethod = $endpoint->getHttpMethod();
            if (!EndpointMethod::isValid($httpMethod)) {
                continue;
            }

            $className   = $endpoint->getControllerClass();
            $classMethod = $endpoint->getControllerMethod();
            if (!$container->has($className)) {
                $container[$className] = function () use ($className) {
                    return new $className();
                };
            }

            /** @var RouteInterface $slimRoute */
            $slimRoute = $forgeSlimApp->$httpMethod(#
                $endpoint->getPath(), $className . ':' . $classMethod#
            )->setName($endpoint->getName());

            $endpointMiddlewares = $middlewareService->filterActiveMiddlewaresForEndpoint($activeMiddlewares, $endpoint);
            $slimRoute->add(new MiddlewarePluginManager($endpointMiddlewares));

            $slimRoute->add(new RenderMiddleware());
            $slimRoute->add(new RouteMiddleware($endpoint));
            $slimRoute->add(new DebugModeMiddleware());
            $slimRoute->add(new SessionMiddleware());
        }
    }
}
