<?php

namespace Oforge\Engine\Core\Controllers\Frontend;

use Oforge\Engine\Core\Abstracts\AbstractController;
use Oforge\Engine\Core\Annotations\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotations\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NotFoundController
 *
 * @package Oforge\Engine\Core\Controllers\Frontend
 * @EndpointClass(path="/404", name="not_found", assetBundles="Frontend")
 */
class NotFoundController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        return $response->withStatus(404);
    }

}
