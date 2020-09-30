<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Auth\Services;

use Oforge\Engine\Auth\Models\User\User;

/**
 * Class BackendLoginService
 *
 * @package Oforge\Engine\Auth\Services
 */
class BackendLoginService extends BaseLoginService {

    /**
     * BackendLoginService constructor.
     */
    public function __construct() {
        parent::__construct(User::class);
    }

}
