<?php

namespace Oforge\Engine\Core\Middlewares;

use Oforge\Engine\Core\Services\ConfigService;

/**
 * Class DebugModeMiddleware
 *
 * @package Oforge\Engine\Core\Middlewares
 */
class DebugModeMiddleware {

    /** @inheritDoc */
    public function __invoke($request, $response, $next) {
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
