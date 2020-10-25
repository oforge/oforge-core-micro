<?php

namespace Oforge\Engine\Crud\Traits\Controller;

use Exception;
use Monolog\Logger;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Crud\Exceptions\EntityNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Trait CrudDeleteActionTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudDeleteActionTrait {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/{id}", method=EndpointMethod::DELETE, assetBundles="", assetBundlesMode=AssetBundleMode::NONE)
     */
    public function deleteAction(Request $request, Response $response, array $args) {
        try {
            $this->crudService->delete($this->model, $args['id']);

            return $response->withStatus(204);
        } catch (EntityNotFoundException $exception) {
            Oforge()->Logger()->logException($exception, 'crud', Logger::WARNING);

            return $response->withStatus(404);
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception, 'crud');

            return $response->withStatus(500);
        }
    }

}
