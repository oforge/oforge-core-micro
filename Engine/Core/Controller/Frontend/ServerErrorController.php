<?php

namespace Oforge\Engine\Core\Controller\Frontend;

use Oforge\Engine\Core\Abstracts\AbstractController;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NotFoundController
 *
 * @package Oforge\Engine\Core\Controller\Frontend
 * @EndpointClass(path="/500", name="server_error", assetBundles="Frontend")
 */
class ServerErrorController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        return $response->withStatus(500);
    }

}
