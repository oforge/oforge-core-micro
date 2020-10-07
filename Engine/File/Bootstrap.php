<?php

namespace Oforge\Engine\File;

use Oforge\Engine\File\Service\FileService;
use Oforge\Engine\File\Service\MimeTypeService;
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
            'file'          => FileService::class,
            'file.mimeType' => MimeTypeService::class,
        ];
        $this->models   = [
            Model\File::class,
            Model\FileMimeType::class,
        ];
    }

    /** @inheritdoc */
    public function install() {
        parent::install();
        /** @var MimeTypeService $mimeTypeService */
        $mimeTypeService = Oforge()->Services()->get('file.mimeType');
        $mimeTypeService->install();
    }


}
