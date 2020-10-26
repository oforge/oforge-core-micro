<?php

namespace Oforge\Engine\Crud;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Crud\Services\GenericCrudService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Crud
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        try {
            Oforge()->Logger()->initLogger('crud');
        } catch (LoggerAlreadyExistException $exception) {
            // nothing to do
        }

        $this->services = [
            'crud' => GenericCrudService::class,
        ];
    }

}
