<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 13.11.2018
 * Time: 11:12
 */

namespace Oforge\Engine\Core\Managers\Slim;

use Oforge\Engine\Core\Models\Plugin\Middleware;

/**
 * Class MiddlewarePluginManager
 *
 * @package Oforge\Engine\Core\Managers\Slim
 */
class MiddlewarePluginManager {
    /** @var Middleware[] $activeMiddlewares */
    private $activeMiddlewares;

    /**
     * MiddlewarePluginManager constructor.
     *
     * @param Middleware[] $activeMiddlewares
     */
    public function __construct($activeMiddlewares) {
        $this->activeMiddlewares = $activeMiddlewares;
    }

    /** @inheritDoc */
    public function __invoke($request, $response, $next) {
        foreach ($this->activeMiddlewares as $middleware) {
            $className = $middleware->getClass();

            if (method_exists($className, 'prepend')) {
                $newResponse = (new $className())->prepend($request, $response);

                if (isset($newResponse)) {
                    if (is_array($newResponse) && sizeof($newResponse) == 2) {
                        $request  = $newResponse[0];
                        $response = $newResponse[1];
                    } else {
                        $response = $newResponse;
                    }
                }
            }
        }

        $stopNext = Oforge()->View()->get('stopNext');

        if (!isset($stopNext)) {
            $response = $next($request, $response);
        }

        foreach ($this->activeMiddlewares as $middleware) {
            $className = $middleware->getClass();

            if (method_exists($className, 'append')) {
                $newResponse = (new $className())->append($request, $response);
                if (isset($newResponse)) {
                    if (is_array($newResponse) && sizeof($newResponse) == 2) {
                        $request  = $newResponse[0];
                        $response = $newResponse[1];
                    } else {
                        $response = $newResponse;
                    }
                }
            }
        }

        return $response;
    }

}
