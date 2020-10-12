<?php

namespace Oforge\Engine\Crud\Controller\Traits;

/**
 * Trait CrudBundleCrudActionsTrait
 *
 * @package Oforge\Engine\Crud\Controller\Traits
 */
trait CrudBundleCrudActionsTrait {
    use CrudIndexActionTrait, CrudCreateActionTrait, CrudReadActionTrait, CrudUpdateActionTrait, CrudDeleteActionTrait;
}
