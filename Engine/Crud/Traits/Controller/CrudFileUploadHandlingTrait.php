<?php

namespace Oforge\Engine\Crud\Traits\Controller;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Crud\Enums\CrudDataAccessLevel;
use Oforge\Engine\Crud\Enums\CrudDataType;
use Oforge\Engine\File\Exceptions\FileImportException;
use Oforge\Engine\File\Exceptions\FileNotFoundException;
use Oforge\Engine\File\Exceptions\MimeTypeNotAllowedException;
use Oforge\Engine\File\Services\FileManagementService;
use Slim\Http\Request;
use Slim\Http\UploadedFile;

/**
 * Trait CrudFileUploadHandlingTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudFileUploadHandlingTrait {

    /**
     * Handles uploaded files (defined by modelProperties).
     *
     * @param array $data
     * @param Request $request
     * @param string $context
     *
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function handleFileUploads(array &$data, Request $request, string $context) {
        $files = $request->getUploadedFiles();
        if (empty($files)) {
            return;
        }
        $walkFiles = function ($files, string $propertyParent = '') use (&$data, &$walkFiles, $context) {
            /** @var UploadedFile[] $files */
            foreach ($files as $key => $value) {
                $propertyPath = ltrim($propertyParent . '.' . $key, '.');
                if (is_array($value)) {
                    $walkFiles($value, $propertyPath);
                } else {
                    $uploadedFile = $value;
                    if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $propertyConfig = ArrayHelper::dotGet($this->modelProperties, $propertyPath, null);
                    if ($propertyConfig === null || CrudDataType::FILE !== $propertyConfig['type']) {
                        continue;
                    }
                    $accessLevel = ($context === 'update' ? CrudDataAccessLevel::UPDATE : CrudDataAccessLevel::CREATE);
                    if ($this->strictWriteMode && ArrayHelper::get($propertyConfig, 'mode', CrudDataAccessLevel::OFF) < $accessLevel) {
                        continue;
                    }
                    $this->handleUploadedFile($data, $propertyPath, $uploadedFile, $context);
                }
            }
        };
        $walkFiles($files);
    }

    /**
     * Handle single uploaded file.
     *
     * @param array $data
     * @param string $propertyPath
     * @param UploadedFile $uploadedFile
     * @param string $context
     *
     * @throws FileImportException
     * @throws FileNotFoundException
     * @throws MimeTypeNotAllowedException
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function handleUploadedFile(array &$data, string $propertyPath, UploadedFile $uploadedFile, string $context) {
        /** @var FileManagementService $fileManagementService */
        $fileManagementService = Oforge()->Services()->get('file');

        $file = $fileManagementService->importUploadedFile($uploadedFile);
        $data = ArrayHelper::dotSet($data, $propertyPath, $file->getId());
    }

}
