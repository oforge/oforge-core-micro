<?php

namespace Oforge\Engine\File;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Models\Config\ConfigType;
use Oforge\Engine\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\FileUpload
 */
class Bootstrap extends AbstractBootstrap {

    /** Bootstrap constructor. */
    public function __construct() {
        // $this->endpoints = [
        //     Controllers\Api\FileController::class,
        // ];

        $this->services = [
            'file.access'     => Services\FileAccessService::class,
            'file.management' => Services\FileManagementService::class,
            'file.mimeType'   => Services\AllowedFileMimeTypeService::class,
            'file.usage'      => Services\FileUsageService::class,
        ];

        $this->models = [
            Models\File::class,
            Models\FileUsage::class,
            Models\FileMimeType::class,
        ];
    }

    /** @inheritdoc */
    public function install() {
        parent::install();
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        /** @var Services\AllowedFileMimeTypeService $mimeTypeService */
        $mimeTypeService = Oforge()->Services()->get('file.mimeType');

        $mimeTypeService->install();

        $configService->add([
            'name'    => 'file_import_mime_type_restriction',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'file',
            'default' => true,
            'label'   => 'config_file_import_mime_type_restriction',
        ]);
    }

}
