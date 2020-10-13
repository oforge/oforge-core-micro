<?php

namespace Oforge\Engine\File\Traits\Service;

use Oforge\Engine\File\Services\FileAccessService;

/**
 * Trait FileAccessServiceTrait
 *
 * @package Oforge\Engine\File\Services\Traits
 */
trait FileAccessServiceTrait {

    /**
     * @return FileAccessService
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function FileAccessService() : FileAccessService {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Oforge()->Services()->get('file.access');
    }

}
