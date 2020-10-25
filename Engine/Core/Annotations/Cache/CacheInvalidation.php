<?php

namespace Oforge\Engine\Core\Annotations\Cache;

/**
 * Class CacheInvalidation
 *
 * @Annotation
 * @Target({"METHOD"})
 * @package Oforge\Engine\Core\Annotations\Cache
 */
class CacheInvalidation {
    /** @var string $slot Slot. */
    private $slot;

    /**
     * CacheInvalidation constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->slot = $config['slot'] ?? 'default';
    }

    /**
     * @return string
     */
    public function getSlot() : string {
        return $this->slot;
    }
}
