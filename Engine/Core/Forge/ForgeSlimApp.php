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

                $trace = str_replace("\n", "<br />\n", $exception->getTraceAsString());
                $file  = $exception->getFile();
                $line  = $exception->getLine();
                $html  = <<<TAG
<h1>Exception: $message</h1>
<dl>
    <dt><strong>File</strong></dt><dd>$file</dd>
    <dt><strong>Line</strong></dt><dd>$line</dd>
    <dt><strong>Trace</strong></dt><dd>$trace</dd>
</dl>
TAG;

                if (Oforge()->Settings()->isDevelopmentMode()) {
                    return $response->write($html)->withStatus(500);
                } else {
                    // TODO something to do???
                    return $response;
                }
            };
        };
        $container['errorHandler']    = $errorHandler;
        $container['phpErrorHandler'] = $errorHandler;

        $container['cookie'] = function ($container) {
            return new Cookies();
        };
    }

    /** @return ForgeSlimApp */
    public static function getInstance() : ForgeSlimApp {
        if (!isset(self::$instance)) {
            self::$instance = new ForgeSlimApp();
        }

        return self::$instance;
    }

    /**
     * @param bool $silent
     *
     * @return ResponseInterface
     * @throws \Throwable
     */
    public function run($silent = false) {
        /** @var ResponseInterface $response */
        $response = $this->getContainer()->get('response');
        /** @var ServerRequestInterface $request */
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
                $message .= ltrim($keyPrefix . '.' . $key, '.') . ' => ' . $value . "\n";;
            }
        }

        return $message;
    }
}
