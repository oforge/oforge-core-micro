<?php

namespace Oforge\Engine\File\Controllers\Api;

use Exception;
use Oforge\Engine\Core\Annotation\Endpoint\AssetBundlesMode;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Core\Controller\Traits\TraitInitializer;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Crud\Controller\Traits\CrudBaseTrait;
use Oforge\Engine\Crud\Controller\Traits\CrudBundleReadActionsTrait;
use Oforge\Engine\Crud\Enum\CrudDataAccessLevel;
use Oforge\Engine\Crud\Enum\CrudDataType;
use Oforge\Engine\File\Exceptions\FileEntryNotFoundException;
use Oforge\Engine\File\Exceptions\FileInUsageException;
use Oforge\Engine\File\Models\File;
use Oforge\Engine\File\Services\FileManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FileController
 *
 * @package Oforge\Engine\File\Controllers\Api
 * @EndpointClass(path="/api/file", name="api_file", assetBundles=null, assetBundlesMode=AssetBundlesMode::NONE)
 */
class FileController {
    use TraitInitializer, CrudBaseTrait, CrudBundleReadActionsTrait;

    /** FileController constructor. */
    public function __construct() {
        [AssetBundlesMode::class, EndpointAction::class, EndpointMethod::class];// Required for imports in nested traits

        $this->model = File::class;

        $this->modelProperties = [
            'id'         => [
                'type' => CrudDataType::ID,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'createdAt'  => [
                'type' => CrudDataType::DATETIME,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'updatedAt'  => [
                'type' => CrudDataType::DATETIME,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'typeGroup'  => [
                'type' => CrudDataType::STRING,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'mimeType'   => [
                'type' => CrudDataType::STRING,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'size'       => [
                'type' => CrudDataType::INT,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'filePath'   => [
                'type' => CrudDataType::STRING,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'uploaderID' => [
                'type' => CrudDataType::STRING,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'meta'       => [
                'type' => 'array',
                'mode' => CrudDataAccessLevel::READ,
            ],
        ];
        $this->callTraitMethod('__construct');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction(path="[/]", method=EndpointMethod::POST, assetBundles="", assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function uploadAction(Request $request, Response $response) {
        /** @var FileManagementService $fileManagementService */
        try {
            $fileManagementService = Oforge()->Services()->get('file.management');
        } catch (ServiceNotFoundException $exception) {
            return $response->withStatus(500);
        }
        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles)) {
            return $response->withStatus(400);
        }
        $options = $request->getParsedBody();
        if (!is_array($options)) {
            $options = [];
        }
        $options = ArrayHelper::extractByKey($options, ['rename', 'meta']);
        if (isset($options['rename'])) {
            $options['rename'] = ArrayHelper::extractByKey($options['rename'], ['filename', 'prefix', 'suffix']);
        }
        if (isset($options['meta'])) {
            $options['meta'] = ArrayHelper::extractByKey($options['meta'], ['uploaderID']);
        }
        $result = [];
        foreach ($uploadedFiles as $uploadedFile) {
            try {
                $file     = $fileManagementService->importUploadedFile($uploadedFile, $options);
                $result[] = $this->prepareEntityDataArray($file, 'create');
            } catch (Exception $exception) {
                $result[] = ['error' => $exception->getMessage()];
            }

        }

        return ResponseHelper::json($response, $result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/{id}", method=EndpointMethod::DELETE, assetBundles="", assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function deleteAction(Request $request, Response $response, array $args) {
        try {
            /** @var FileManagementService $fileManagementService */
            $fileManagementService = Oforge()->Services()->get('file.management');
            $fileManagementService->delete($args['id']);

            return $response->withStatus(204);
        } catch (FileEntryNotFoundException $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->withStatus(404);
        } catch (FileInUsageException $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->withStatus(409);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return $response->withStatus(500);
        }
    }

}
