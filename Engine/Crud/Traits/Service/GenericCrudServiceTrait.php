<?php

namespace Oforge\Engine\Crud\Traits\Service;

use Oforge\Engine\Crud\Service\GenericCrudService;

/**
 * Trait GenericCrudServiceTrait
 *
 * @package Oforge\Engine\Crud\Traits\Service
 */
trait GenericCrudServiceTrait {
    /** @var GenericCrudService $genericCrudService */
    private $genericCrudService;

    /**
     * @return GenericCrudService
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function GenericCrudService() : GenericCrudService {
        if (!isset($this->genericCrudService)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->genericCrudService = Oforge()->Services()->get('crud');
        }

        return $this->genericCrudService;
    }

}
