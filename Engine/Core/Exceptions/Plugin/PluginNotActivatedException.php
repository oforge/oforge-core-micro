<?php

namespace Oforge\Engine\Core\Exceptions\Plugin;

use Exception;

/**
 * Class PluginNotActivatedException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class PluginNotActivatedException extends Exception {

    /**
     * PluginNotInstalledException constructor.
     *
     * @param string $pluginName
     */
    public function __construct(string $pluginName) {
        parent::__construct("The plugin '$pluginName' is not activated. You have to activate it first.");
    }

}
