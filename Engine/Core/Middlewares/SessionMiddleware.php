<?php

namespace Oforge\Engine\Core\Middlewares;

use Oforge\Engine\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Services\ConfigService;
use Oforge\Engine\Core\Services\Session\SessionService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SessionMiddleware
 *
 * @package Oforge\Engine\Core\Middlewares
 */
class SessionMiddleware {

    /**
     * @param RequestInterface $request PSR7 request
     * @param ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return ResponseInterface
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next) {
        /** @var SessionService $sessionService */
        $sessionService = Oforge()->Services()->get('session');
        $sessionService->start();
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        if ($configService->get('debug_mode')) {
            // for debugging purposes
            if ($configService->get('debug_session')) {
                Oforge()->View()->assign(['debug.session' => $_SESSION]);
            }
        }

        return $next($request, $response);
    }

}
