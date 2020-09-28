<?php

namespace Oforge\Engine\TemplateEngine\Core\Abstracts;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\TemplateEngine\Core\Services\ScssVariableService;

abstract class AbstractTemplate {
    public $parent;
    protected $context;
    protected $templateVariables = [];

    /**
     * AbstractTemplate constructor
     */
    public function __construct() {
        $this->context = static::class;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws InvalidScssVariableException
     */
    public function registerTemplateVariables() {
        /** @var ScssVariableService $scssVariables */
        $scssVariables = Oforge()->Services()->get('scss.variables');
        foreach ($this->templateVariables as $templateVariable) {
            $templateVariable['context'] = $this->context;
            $scssVariables->add($templateVariable);
        }
    }

    /**
     * @param array $variables
     */
    protected function addTemplateVariables(array $variables) {
        $this->templateVariables = array_merge($variables, $this->templateVariables);
    }
}
