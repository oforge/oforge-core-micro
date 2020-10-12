<?php

namespace Oforge\Engine\Crud\Controller\Traits;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Monolog\Logger;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Crud\Exceptions\EntityAlreadyExistException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CrudCreateActionTrait
 *
 * @package Oforge\Engine\Crud\Controller\Traits
 */
trait CrudCreateActionTrait {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction(path="", method=EndpointMethod::POST, assetBundles="", assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function createAction(Request $request, Response $response) {
        if (empty($data = $request->getParsedBody())) {
            return $response->withStatus(400);
        } else {
            try {
                $data = ArrayHelper::mergeRecursive($this->prepareCreateDefaultData(), $data, true);
                if (method_exists($this, 'handleFileUploads')) {
                    $this->handleFileUploads($data, $request, 'create');
                }
                $data   = $this->convertData($data, 'create');
                $entity = $this->crudService->create($this->model, $data);

                return ResponseHelper::json($response, $this->prepareEntityDataArray($entity, 'create'))->withStatus(201);
            } catch (EntityAlreadyExistException | UniqueConstraintViolationException $exception) {
                Oforge()->Logger()->logException($exception, 'crud', Logger::WARNING);

                return $response->withStatus(409);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception, 'crud');

                return $response->withStatus(500);
            }
        }
    }

    private function prepareCreateDefaultData() : array {
        $data = [];
        foreach ($this->modelProperties as $property => $propertyConfig) {
            if (ArrayHelper::dotExist($propertyConfig, 'config.default')) {
                $data = ArrayHelper::dotSet($data, $property, ArrayHelper::dotGet($propertyConfig, 'config.default'));
            }
        }

        return $data;
    }

}
