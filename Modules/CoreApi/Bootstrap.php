<?php

namespace Oforge\Modules\CoreApi;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Modules\CoreApi
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            Controllers\Api\KeyValueController::class,
        ];
    }

}
