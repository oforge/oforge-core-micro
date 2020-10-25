<?php

namespace Oforge\Engine\Core\Managers\Slim;

use Oforge\Engine\Core\Models\Endpoint\Endpoint;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * Class RouteMiddleware
 *
 * @package Oforge\Engine\Core\Managers\Slim
 */
class RouteMiddleware {
    /** @var Endpoint */
    protected $endpoint = null;

    /**
     * RouteMiddleware constructor.
     *
     * @param Endpoint $endpoint
     */
    public function __construct(Endpoint $endpoint) {
        $this->endpoint = $endpoint;
    }

    /** @inheritDoc */
    public function __invoke(Request $request, Response $response, $next) {
        $routeInfo = $request->getAttribute('routeInfo');
        list($uriBasePath, $uriBaseUrl) = $this->getUriBaseData($request);

        Oforge()->View()->assign([
            'meta' => [
                'route' => array_merge($this->endpoint->toArray(), [
                    'basePath' => $uriBasePath,
                    'baseUrl'  => $uriBaseUrl,
                    'params'   => $routeInfo[2],
                    'query'    => $request->getQueryParams(),
                    'url'      => [
                        'path'  => $request->getUri()->getPath(),
                        'query' => $request->getUri()->getQuery(),
                    ],
                ]),
            ],
        ]);

        return $next($request, $response);
    }

    /**
     * Get system base data (base path & url) of slim.
     *
     * @param Request $request
     *
     * @return array
     */
    private function getUriBaseData(Request $request) : array {
        /** @var Uri $uri */
        $uri      = $request->getUri();
        $scheme   = $uri->getScheme();
        $host     = $uri->getHost();
        $port     = $uri->getPort();
        $basePath = $uri->getBasePath();

        $scheme   = ($scheme === '' ? '' : ($scheme . '://'));
        $port     = ($port === null ? '' : (':' . $port));
        $basePath = rtrim($basePath, '/');

        $baseUrl = $scheme . $host . $port . $basePath;

        return [$basePath, $baseUrl];
    }

}
