<?php

namespace Oforge\Engine\Auth\Middlewares;

use Oforge\Engine\Auth\Controller\SecureController;
use Oforge\Engine\Auth\Models\User\User;
use Oforge\Engine\Auth\Services\AuthService;
use Oforge\Engine\Auth\Services\PermissionService;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\ResponseHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SecureMiddleware
 *
 * @package Oforge\Engine\Auth\Middleware
 */
class SecureMiddleware {
    /** @var string $userClass */
    protected $userClass = User::class;
    /** @var string $viewUserDataKey */
    protected $viewUserDataKey = 'user';
    /** @var string $invalidRedirectPathName The named path for redirects */
    protected $invalidRedirectPathName = '';

    /**
     * @param array|null $user
     * @param array|null $permission
     *
     * @return bool
     */
    public static function checkUserPermission(?array $user, ?array $permission) {
        return ($user !== null && $permission !== null
                && isset($user['role'])
                && isset($user['type'])
                && $user['type'] === $permission['type']
                && $user['role'] <= $permission['role']);
    }

    /**
     * Middleware call before the controller call
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response|null
     * @throws ServiceNotFoundException
     */
    public function prepend(Request $request, Response $response) : ?Response {
        $user = null;
        if (isset($_SESSION['auth'])) {
            $auth = $_SESSION['auth'];
            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);
            if ($user !== null && $user['type'] === $this->userClass) {
                Oforge()->View()->assign([
                    $this->viewUserDataKey => $user,
                ]);
            }
        }
        $routeController  = Oforge()->View()->get('meta')['route'];
        $controllerClass  = $routeController['controllerClass'];
        $controllerMethod = $routeController['controllerMethod'];
        if (is_subclass_of($controllerClass, SecureController::class)) {
            /** @var PermissionService $permissionService */
            $permissionService = Oforge()->Services()->get('permissions');
            $permission        = $permissionService->get($controllerClass, $controllerMethod);
        } else {
            $permission = ['role' => null, 'type' => $this->userClass];
        }
        if (static::checkUserPermission($user, $permission)) {
            //nothing to do. proceed
        } else {
            Oforge()->View()->assign(['stopNext' => true]);
            $referrer = $request->getUri()->getPath();
            if (!empty($request->getUri()->getQuery())) {
                $referrer .= '?' . $request->getUri()->getQuery();
            }
            $_SESSION['login_redirect_url'] = $referrer;
            if (!empty($this->invalidRedirectPathName)) {
                return ResponseHelper::redirect($response, $this->invalidRedirectPathName);
            }

            return ResponseHelper::redirectToUri($response, '/');
        }

        return $response;
    }
}
