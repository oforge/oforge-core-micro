<?php

namespace Oforge\Engine\Core\Helper;

use Slim\Http\Response;

/**
 * Class ResponseHelper
 *
 * @package Oforge\Engine\Core\Helper
 */
class ResponseHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * Prepare json response with json header, status code 200 and content data.
     *
     * @param Response $response
     * @param array $data
     *
     * @return Response
     */
    public static function json(Response $response, array $data) : Response {
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->withJson($data);
    }

    /**
     * @param Response $response
     * @param string|null $routeName If null, the routeName by meta.route.name.
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return Response
     */
    public static function redirect(Response $response, ?string $routeName = null, array $namedParams = [], array $queryParams = []) : Response {
        if ($routeName === null) {
            $routeName = Oforge()->View()->get('meta.route.name');
        }
        $uri = RouteHelper::getSlimUrl($routeName, $namedParams, $queryParams);

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Response $response
     * @param string $uri
     *
     * @return Response
     */
    public static function redirectToUri(Response $response, string $uri) : Response {
        return $response->withRedirect($uri, 302);
    }

}
