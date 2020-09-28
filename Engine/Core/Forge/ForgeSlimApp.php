<?php

namespace Oforge\Engine\Core\Forge;

use Error;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Slim\Exception\InvalidMethodException;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class App
 * An extension of the SlimApp Container.
 * See https://www.slimframework.com/
 *
 * @package Oforge\Engine\Core
 */
class ForgeSlimApp extends SlimApp {
    /** @var ForgeSlimApp $instance */
    protected static $instance = null;

    /**
     * App constructor.
     * Defines the slim default error behaviour.
     */
    public function __construct() {
        parent::__construct();
        $container    = $this->getContainer();
        $errorHandler = function ($container) {
            return function (Request $request, Response $response, $exception) use ($container) {
                /** @var Exception|Error $exception */
                $message = $exception->getMessage();
                Oforge()->Logger()->get()->error($message, $exception->getTrace());

                echo  $message, "<br>", $exception->getTraceAsString();
                die;


                if (Oforge()->Settings()->isDevelopmentMode()) {
                    return $response->withStatus(500);
                } else {
                    Oforge()->Logger()->get()->error($message, $exception->getTrace());
                }
            };
        };

        $container['errorHandler']    = $errorHandler;
        $container['phpErrorHandler'] = $errorHandler;

        $container['cookie'] = function ($container) {
            return new Cookies();
        };
    }

    /**
     * @return ForgeSlimApp
     */
    public static function getInstance() : ForgeSlimApp {
        if (!isset(self::$instance)) {
            self::$instance = new ForgeSlimApp();
        }

        return self::$instance;
    }

    /**
     * Start the session
     *
     * @param int $lifetimeSeconds
     * @param string $path
     * @param null $domain
     * @param null $secure
     */
    public function sessionStart($lifetimeSeconds = 0, $path = '/', $domain = null, $secure = null) {
        $sessionStatus = session_status();

        if ($sessionStatus != PHP_SESSION_ACTIVE) {
            session_name("oforge_session");
            if (!empty($_SESSION['deleted_time'])
                && $_SESSION['deleted_time'] < time() - 180) {
                session_destroy();
            }
            // Set the domain to default to the current domain.
            $domain = isset($domain) ? $domain : $_SERVER['SERVER_NAME'];

            // Set the default secure value to whether the site is being accessed with SSL
            $secure = isset($secure) ? $secure : isset($_SERVER['HTTPS']) ? true : false;

            // Set the cookie settings and start the session
            session_set_cookie_params($lifetimeSeconds, $path, $domain, $secure, true);
            session_start();
            $_SESSION['created_time'] = time();
        }
    }

    public function run($silent = false) {
        /**
         * @var $response ResponseInterface
         */
        $response = $this->getContainer()->get('response');

        /**
         * @var $request ServerRequestInterface
         */
        $request = $this->getContainer()->get('request');

        try {
            $response = $this->process($request, $response);
        } catch (InvalidMethodException $e) {
            $response = $this->processInvalidMethod($e->getRequest(), $response);
        }

        $response = $this->finalize($response);

        if (!$silent) {
            $this->respond($response);
        }
        return $response;
    }

    /**
     * @param array $data
     * @param string $keyPrefix
     *
     * @return string
     */
    private function createLog(array $data, string $keyPrefix = '') {
        $message = '';
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $message .= $this->createLog($value, ltrim($keyPrefix . '.' . $key, '.'));
            } else {
                $message .= (empty($keyPrefix) ? '' : ($keyPrefix . '.')) . $key . ' => ' . $value . "\n";;
            }
        }

        return $message;
    }
}
