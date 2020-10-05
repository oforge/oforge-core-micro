<?php

namespace Oforge\Engine\Core\Controller\Api;

use Oforge\Engine\Core\Abstracts\AbstractController;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NotFoundController
 *
 * @package Oforge\Engine\Core\Controller\Frontend
 * @EndpointClass(path="/api/ping", name="ping")
 */
class PingController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        return ResponseHelper::json($response, ['message' => 'Ping']);
    }

}
