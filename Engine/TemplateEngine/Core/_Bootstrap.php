<?php

namespace Oforge\Engine\TemplateEngine\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\TemplateEngine\Core\Manager\TemplateManager;
use Oforge\Engine\TemplateEngine\Core\Manager\ViewManager;
use Oforge\Engine\TemplateEngine\Core\Middleware\AssetsMiddleware;
use Oforge\Engine\TemplateEngine\Core\Models\ScssVariable;
use Oforge\Engine\TemplateEngine\Core\Models\Template\Template;
use Oforge\Engine\TemplateEngine\Core\Services\CssAssetService;
use Oforge\Engine\TemplateEngine\Core\Services\JsAssetService;
use Oforge\Engine\TemplateEngine\Core\Services\ScssVariableService;
use Oforge\Engine\TemplateEngine\Core\Services\StaticAssetService;
use Oforge\Engine\TemplateEngine\Core\Services\TemplateAssetService;
use Oforge\Engine\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\TemplateEngine\Core\Services\TemplateRenderService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\TemplateEngine\Core
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            'scss.variables'      => ScssVariableService::class,
            'template.render'     => TemplateRenderService::class,
            'template.management' => TemplateManagementService::class,
            'assets.template'     => TemplateAssetService::class,
            'assets.js'           => JsAssetService::class,
            'assets.css'          => CssAssetService::class,
            'assets.static'       => StaticAssetService::class,
        ];

        $this->models = [
            Template::class,
            ScssVariable::class,
        ];

        $this->middlewares = [
            '*' => ['class' => AssetsMiddleware::class, 'position' => 0],
        ];

        $this->order = 1;

        //Oforge()->setTemplateManager(TemplateManager::getInstance());
        //Oforge()->setViewManager(ViewManager::getInstance());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     */
    public function activate() {
        Oforge()->Templates()->init();

        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get('template.management');

        $templateName = $templateManagementService->getActiveTemplate()->getName();

        $scopes = ['Frontend', 'Backend'];

        foreach ($scopes as $scope) {
            if (!Oforge()->Services()->get('assets.css')->isBuild($scope)) {
                Oforge()->Services()->get('assets.template')->build($templateName, $scope);
            }
        }
    }

}
