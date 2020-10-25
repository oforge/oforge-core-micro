<?php

namespace Oforge\Engine\Auth\Controllers;

use Oforge\Engine\Auth\Models\User\User;
use Oforge\Engine\Auth\Services\PermissionService;
use Oforge\Engine\Core\Abstracts\AbstractController;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;

/**
 * Class SecureController
 *
 * @package Oforge\Engine\Auth\Controllers
 */
class SecureController extends AbstractController {
    /** @var string $secureControllerUserClass */
    protected $secureControllerUserClass = User::class;

    public function initPermissions() {
    }

    /**
     * @param string $method
     * @param string $userType
     * @param int|null $role
     */
    protected function ensurePermission(string $method, ?int $role = null) {
        static::ensurePermissions([$method], $role);
    }

    /**
     * @param string[] $methods
     * @param string $userType
     * @param int|null $role
     */
    protected function ensurePermissions($methods, ?int $role = null) {
        try {
            /** @var PermissionService $permissionService */
            $permissionService = Oforge()->Services()->get('permissions');
            foreach ($methods as $method) {
                $permissionService->put(static::class, $method, $this->secureControllerUserClass, $role);
            }
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

}
