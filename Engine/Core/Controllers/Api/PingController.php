<?php

namespace Oforge\Engine\Core\Controllers\Api;

use Oforge\Engine\Core\Abstracts\AbstractController;
use Oforge\Engine\Core\Annotations\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotations\Endpoint\EndpointClass;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Core\Services\PingService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NotFoundController
 *
 * @package Oforge\Engine\Core\Controllers\Frontend\Api
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
        /** @var PingService $pingService */
        $pingService = Oforge()->Services()->get('ping');

        return ResponseHelper::json($response, ['message' => $pingService->me()]);
    }

}
