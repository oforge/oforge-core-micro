<?php

namespace Oforge\Engine\Core\Annotations\Endpoint;

/**
 * Class AssetBundleMode
 *
 * @package Oforge\Engine\Core\Annotations\Endpoint
 */
class AssetBundleMode {
    public const MERGE    = 'merge';
    public const NONE     = 'none';
    public const OVERRIDE = 'override';

    private function __construct() {
    }

}
