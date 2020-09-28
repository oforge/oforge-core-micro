<?php

namespace Oforge\Engine\Core\Annotation\Endpoint;

/**
 * Class AssetBundlesMode
 *
 * @package Oforge\Engine\Core\Annotation\Endpoint
 */
class AssetBundlesMode {
    public const MERGE    = 'merge';
    public const NONE     = 'none';
    public const OVERRIDE = 'override';

    private function __construct() {
    }

}
