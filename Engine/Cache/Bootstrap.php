<?php

namespace Oforge\Engine\Cache;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Cache
 */
class Bootstrap extends AbstractBootstrap {

    /** Bootstrap constructor. */
    public function __construct() {
        $this->order = 1;
    }

}
