<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        // AbstractBootstrap configuration
        override(\Oforge\Engine\Core\Abstracts\AbstractBootstrap::getConfiguration(0), map([
            'commands' => '',
        ]));
        override(\Oforge\Engine\Core\Abstracts\AbstractBootstrap::setConfiguration(0), map([
            'commands' => '',
        ]));
    }

}
