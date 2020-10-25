<?php

namespace Oforge\Engine\Core\Annotation\Endpoint;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * Class EndpointClass
 *
 * @Annotation
 * @Target({"CLASS"})
 * @package Oforge\Engine\Core\Annotation\Endpoint
 */
class EndpointClass {
    /**
     * Base url path. (Required)
     *
     * @var string $path
     */
    private $path;
    /**
     * Global asset bundles for this class. Overridable by Endpoint#assetBundles. Default=NULL(none).
     *
     * @var string|string[]|null $assetBundles
     */
    private $assetBundles;
    /**
     * Mode of how assetsBundles are created for endpoint. AssetBundleMode::OVERRIDE (default) or AssetBundleMode::MERGE or AssetBundleMode::NONE.
     *
     * @var string|null $assetBundles
     */
    private $assetBundleMode = null;
    /**
     * Optional route name prefix (suffixed by Endpoint#name).
     *
     * @var string $name
     */
    private $name;
    /**
     * Optional global route order. Overridable by Endpoint#order.
     *
     * @var int|null $order
     */
    private $order;
    /**
     * Optional strict Action-suffixed-action-method mode.
     *
     * @var bool $strictActionSuffix
     */
    private $strictActionSuffix;

    /**
     * EndpointClass constructor.
     *
     * @param array $config
     *
     * @throws AnnotationException
     */
    public function __construct(array $config) {
        $this->assetBundles    = $config['assetBundles'] ?? null;
        $this->assetBundleMode = $config['assetBundlesMode'] ?? null;

        $this->name  = $config['name'] ?? '';
        $this->order = $config['order'] ?? null;
        $this->path  = $config['path'] ?? '';

        $this->strictActionSuffix = $config['strictActionSuffix'] ?? true;
    }

    /**
     * @param string $class
     *
     * @throws AnnotationException
     */
    public function checkRequired(string $class) {
        if (!isset($this->path)) {
            throw new AnnotationException (sprintf('Required attribute "%s" of %s is null.', 'path', $class));
        }
    }

    /**
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * @return string|string[]|null
     */
    public function getAssetBundles() {
        return $this->assetBundles;
    }

    /**
     * @return string|null
     */
    public function getAssetBundleMode() : ?string {
        return $this->assetBundleMode;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getOrder() : ?int {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isStrictActionSuffix() : bool {
        return $this->strictActionSuffix;
    }

}
