<?php

namespace Oforge\Engine\Core\Annotations\Cache;

/**
 * Class Cache
 *
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 * @package Oforge\Engine\Core\Annotations\Cache
 */
class Cache {
    /** @var string $slot Slot. */
    private $slot;
    /** @var string $duration */
    private $duration;
    /** @var bool $enabled */
    private $enabled;

    /**
     * Cache constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->slot     = $config['slot'] ?? 'default';
        $this->duration = $config['duration'] ?? 'P1H';
        $this->enabled  = $config['enabled'] ?? true;
    }

    /**
     * @return string
     */
    public function getSlot() : string {
        return $this->slot;
    }

    /**
     * @return string
     */
    public function getDuration() : string {
        return $this->duration;
    }

    /**
     * @return bool
     */
    public function isEnabled() : bool {
        return $this->enabled;
    }

}
