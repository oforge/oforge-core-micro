<?php

namespace Oforge\Engine\Image;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Exceptions\LoggerAlreadyExistException;
use Oforge\Engine\Core\Models\Config\ConfigType;
use Oforge\Engine\Core\Services\ConfigService;
use Oforge\Engine\Image\Enums\ImageConstants;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\FileUpload
 */
class Bootstrap extends AbstractBootstrap {

    /** Bootstrap constructor. */
    public function __construct() {
        try {
            Oforge()->Logger()->initLogger(Enums\ImageConstants::LOGGER);
        } catch (LoggerAlreadyExistException $exception) {
            // nothing to do
        }

        $this->services = [
            'image'         => Services\ImageService::class,
            'image.cleanup' => Services\ImageCleanupService::class,
        ];

        $this->order = 5;
    }

    /** @inheritdoc */
    public function install() {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'        => 'image_upload_thumbnail_widths',
            'type'        => ConfigType::STRING,
            'group'       => 'image',
            'required'    => true,
            'default'     => ImageConstants::DEFAULT_UPLOAD_THUMBNAIL_WIDTHS,
            'label'       => 'config_image_upload_thumbnail_widths',
            'description' => 'config_description_image_upload_thumbnail_widths',
            // 'description' => 'Comma separated list with thumbnail sizes (widths)'
        ]);
        $configService->add([
            'name'        => 'image_access_quality',
            'type'        => ConfigType::INTEGER,
            'group'       => 'image',
            'required'    => true,
            'default'     => ImageConstants::DEFAULT_QUALITY,
            'label'       => 'config_image_access_quality',
            'description' => 'config_description_image_access_quality',
        ]);
    }

}
