<?php

namespace Oforge\Engine\Crud\Traits\Controller;

use Exception;
use Monolog\Logger;
use Oforge\Engine\Core\Annotations\Endpoint\AssetBundleMode;
use Oforge\Engine\Core\Annotations\Endpoint\EndpointAction;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Crud\Exceptions\EntityNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Trait CrudReadActionTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudReadActionTrait {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/{id}", method=EndpointMethod::GET, assetBundles="", AssetBundleMode=AssetBundleMode::NONE)
     */
    public function readAction(Request $request, Response $response, array $args) {
        try {
            $entity = $this->crudService->getById($this->model, $args['id']);
            if ($entity === null) {
                throw new EntityNotFoundException($this->model, $args['id']);
            }

            return ResponseHelper::json($response, $this->prepareOutputData($entity, 'get'));
        } catch (EntityNotFoundException $exception) {
            Oforge()->Logger()->logException($exception, 'crud', Logger::WARNING);

            return $response->withStatus(404);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception, 'crud');

            return $response->withStatus(500);
        }
    }

}
