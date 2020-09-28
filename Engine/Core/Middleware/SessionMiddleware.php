<?php

namespace Oforge\Engine\Core\Middleware;

use Oforge\Engine\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Services\ConfigService;
use Oforge\Engine\Core\Services\Session\SessionManagementService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class SessionMiddleware
 *
 * @package Oforge\Engine\Core\Middleware
 */
class SessionMiddleware {

    /**
     * @param ServerRequestInterface $request PSR7 request
     * @param ResponseInterface $response PSR7 response
     * @param callable $next Next middleware
     *
     * @return mixed
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function __invoke($request, $response, $next) {
        /** @var SessionManagementService $sessionManager */
        $sessionManager = Oforge()->Services()->get('session.management');
        $sessionManager->sessionStart();
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $debugMode     = $configService->get('debug_mode');

        if ($debugMode) {
            /** for debugging purposes */
            $debugSession  = $configService->get('debug_session');
            if ($debugSession) {
                Oforge()->View()->assign(['debug.session' => $_SESSION]);
            }
        }

        return $next($request, $response);
    }

}
