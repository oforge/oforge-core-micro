<?php

namespace Oforge\Engine\Crud\Controller\Traits;

use Exception;
use Monolog\Logger;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Oforge\Engine\Crud\Exceptions\EntityNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Trait CrudUpdateActionTrait
 *
 * @package Oforge\Engine\Crud\Controller\Traits
 */
trait CrudUpdateActionTrait {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/{id}", method=EndpointMethod::PATCH, assetBundles="", assetBundlesMode=AssetBundlesMode::NONE)
     */
    public function updateAction(Request $request, Response $response, array $args) {
        if (empty($data = $request->getParsedBody())) {
            return $response->withStatus(400);
        } else {
            try {
                if (method_exists($this, 'handleFileUploads')) {
                    $this->handleFileUploads($data, $request, 'update');
                }
                $data   = $this->convertData($data, 'update');
                $entity = $this->crudService->update($this->model, $args['id'], $data);

                return ResponseHelper::json($response, $this->prepareEntityDataArray($entity, 'update'));
            } catch (EntityNotFoundException $exception) {
                Oforge()->Logger()->logException($exception, 'crud', Logger::WARNING);

                return $response->withStatus(404);
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception, 'crud');

                return $response->withStatus(500);
            }
        }
    }

}
