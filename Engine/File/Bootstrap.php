<?php

namespace Oforge\Engine\File;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\FileUpload
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            'file'          => Services\FileService::class,
            'file.import'   => Services\FileImportService::class,
            'file.mimeType' => Services\AllowedFileMimeTypeService::class,
            // 'file.usage'    => Services\FileUsageService::class,
        ];
        $this->models   = [
            Models\File::class,
            // Models\FileUsage::class,
            Models\FileMimeType::class,
        ];
    }

    /** @inheritdoc */
    public function install() {
        parent::install();
        /** @var Services\AllowedFileMimeTypeService $mimeTypeService */
        $mimeTypeService = Oforge()->Services()->get('file.mimeType');
        $mimeTypeService->install();
    }

}
