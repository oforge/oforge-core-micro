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
