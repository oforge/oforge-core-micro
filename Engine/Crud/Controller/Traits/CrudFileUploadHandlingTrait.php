<?php

namespace Oforge\Engine\Crud\Controller\Traits;

use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Manager\Events\Event;
use Oforge\Engine\Crud\Enum\CrudDataType;
use Slim\Http\Request;
use Slim\Http\UploadedFile;

trait CrudFileUploadHandlingTrait {

    /**
     * Handles uploaded files (defined by modelProperties).
     *
     * @param array $data
     * @param Request $request
     * @param string $context
     */
    protected function handleFileUploads(array &$data, Request $request, string $context) {
        $files = $request->getUploadedFiles();
        if (empty($files)) {
            return;
        }
        $walkFiles = function (array $files, string $propertyParent = '') use (&$data, &$walkFiles, $context) {
            foreach ($files as $key => $value) {
                $propertyPath = ltrim($propertyParent . '.' . $key, '.');
                if (is_array($value)) {
                    $walkFiles($value, $propertyPath);
                } else {
                    $file = $value;
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        return;
                    }
                    $modelProperty = ArrayHelper::dotGet($this->modelProperties, $propertyPath, null);
                    if ($modelProperty === null) {
                        return;
                    }
                    if (CrudDataType::FILE !== $modelProperty['type']) {
                        return;
                    }
                    $this->handleUploadedFile($data, $propertyPath, $file, $context);
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
     */
    protected function handleUploadedFile(array &$data, string $propertyPath, UploadedFile $uploadedFile, string $context) {
        $result = Oforge()->Events()->trigger(Event::create('crud.handleUploadedFile', [
            'file'    => $uploadedFile,
            'model'   => $this->model,
            'context' => $context,
        ]), null, true);
        $data   = ArrayHelper::dotSet($data, $propertyPath, $result);
        // TODO replace later Core-FileModule
        // /** @var FileService $fileService */
        // $fileService = Oforge()->Services()->get('file');
        // $file = $fileService->addUploadedFile($uploadedFile);
        // if ($file !== null) {
        //     $data = ArrayHelper::dotSet($data, $propertyPath, $file->getID());
        // }
    }

}
