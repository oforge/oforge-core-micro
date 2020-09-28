<?php

namespace Oforge\Engine\Core\Manager\Slim;

use Oforge\Engine\Core\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RenderMiddleware
 *
 * @package Oforge\Engine\Core\Manager\Slim
 */
class RenderMiddleware {

    /** @inheritDoc */
    public function __invoke($request, $response, $next) {
        $data = [];

        $response = $next($request, $response);
        if (empty($data)) {
            $data = Oforge()->View()->fetch();
        } else {
            $fetchedData = Oforge()->View()->fetch();

            $data = ArrayHelper::mergeRecursive($data, $fetchedData);
        }

        return Oforge()->View()->render($request, $response, $data);
    }

}
