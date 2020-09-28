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

        return $this->json($request, $response, ['message' => 'Ping']);
    }

}
