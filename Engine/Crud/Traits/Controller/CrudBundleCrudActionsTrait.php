<?php

namespace Oforge\Engine\Crud\Traits\Controller;

/**
 * Trait CrudBundleCrudActionsTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudBundleCrudActionsTrait {
    use CrudIndexActionTrait, CrudCreateActionTrait, CrudReadActionTrait, CrudUpdateActionTrait, CrudDeleteActionTrait;
}
