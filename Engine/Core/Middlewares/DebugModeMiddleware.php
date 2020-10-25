<?php

namespace Oforge\Engine\Core\Middlewares;

use Oforge\Engine\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Services\ConfigService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DebugModeMiddleware
 *
 * @package Oforge\Engine\Core\Middlewares
 */
class DebugModeMiddleware {

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
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $debugMode     = $configService->get('debug_mode');
        if ($debugMode) {
            $debugData    = [];
            $debugConsole = $configService->get('debug_console');
            if ($debugConsole) {
                $debugData['console'] = true;
            }
            Oforge()->View()->assign(['debug' => $debugData]);
        }

        return $next($request, $response);
    }

}
