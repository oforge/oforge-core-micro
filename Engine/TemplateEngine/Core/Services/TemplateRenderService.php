<?php
/**
 * Created by PhpStorm.
 * User: Matthaeus.Schmedding
 * Date: 07.11.2018
 * Time: 10:39
 */

namespace Oforge\Engine\TemplateEngine\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Exceptions\DependencyNotResolvedException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\Core\Models\Plugin\Plugin;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TemplateRenderService
 *
 * @package Oforge\Engine\TemplateEngine\Core\Services
 */
class TemplateRenderService
{
    /**
     * This render function can be called either from a module controller or a template controller.
     * It checks, whether a template path based on the controllers namespace and the function name exists
     * [e.g.: Oforge/Engine/Modules/Test/Controller/Frontend/HomeController:indexAction => /Themes/$currentTheme/Test/Frontend/Home/Index.twig].
     * If the template is found, it gets rendered by the template engine, the fallback is a json response
     *
     * @param Request $request
     * @param Response $response
     * @param $data
     *
     * @return ResponseInterface|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     */
    public function render(Request $request, Response $response, $data)
    {

        if (isset($data['json']) && is_array($data['json'])) {
            return $this->renderJson($request, $response, $data['json']);
        }

        return $this->renderJson($request, $response, $data);
    }

    /**
     * Send a JSON response
     *
     * @param Request $request
     * @param Response $response
     * @param array $data
     *
     * @return Response
     */
    private function renderJson(Request $request, Response $response, array $data)
    {
        return $response->withHeader('Content-Type', 'application/json')->withJson($data);
    }
}
