<?php

namespace Oforge\Engine\Core\Exceptions\Plugin;

use Exception;

/**
 * Class CouldNotInstallPluginException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class CouldNotInstallPluginException extends Exception {
    /** @var string[] $dependencies */
    private $dependencies;

    /**
     * CouldNotInstallPluginException constructor.
     *
     * @param string $className
     * @param string[] $dependencies
     */
    public function __construct(string $className, $dependencies) {
        parent::__construct("The plugin '$className' could not be started due to missing dependencies. Missing plugins: " . implode(', ', $dependencies));
        $this->dependencies = $dependencies;
    }

    /** @return string[] */
    public function getDependencies() {
        return $this->dependencies;
    }

}
