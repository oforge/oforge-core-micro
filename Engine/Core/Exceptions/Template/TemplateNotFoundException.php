<?php

namespace Oforge\Engine\Core\Exceptions\Template;

use Exception;

/**
 * Class TemplateNotFoundException
 *
 * @package Oforge\Engine\Core\Exceptions
 */
class TemplateNotFoundException extends Exception {

    /**
     * TemplateNotFoundException constructor.
     *
     * @param string $templateName
     */
    public function __construct(string $templateName) {
        parent::__construct("Template with name '$templateName' not found!");
    }

}
