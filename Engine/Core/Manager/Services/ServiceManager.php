<?php

namespace Oforge\Engine\Core\Manager\Services;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Oforge\Engine\Core\Annotation\Cache\Cache;
use Oforge\Engine\Core\Annotation\Cache\CacheInvalidation;
use Oforge\Engine\Core\Exceptions\ServiceAlreadyExistException;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class ServiceManager
 *
 * @package Oforge\Engine\Core\Manager\Services
 */
class ServiceManager {
    /**
     * @var ServiceManager $instance
     */
    protected static $instance = null;
    /**
     * @var array $services
     */
    protected $services = [];

    protected function __construct() {
    }

    /**
     * @return ServiceManager
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ServiceManager();
        }

        return self::$instance;
    }

    /**
     * Find a specific service by name.
     *
     * @param $name
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get($name) {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        throw new ServiceNotFoundException($name);
    }

    /**
     * Set a specific service instance by name.
     *
     * @param $name
     * @param $instance
     *
     * @return mixed
     */
    public function set($name, $instance) {
        $this->services[$name] = $instance;
    }


    /**
     * Get all (unsorted) service names.
     *
     * @return string[]
     */
    public function getServiceNames() {
        return array_keys($this->services);
    }

    /**
     * Register an array of services. Array of name-classname-pairs.
     *
     * @param array $services
     *
     * @throws ServiceAlreadyExistException
     */
    public function register(array $services) {
        foreach ($services as $name => $className) {
            $this->registerService($name, $className);
        }
    }

    /**
     * Register a specific service by name
     *
     * @param string $name
     * @param string $className
     *
     * @throws ServiceAlreadyExistException
     */
    protected function registerService(string $name, string $className) {
        if (isset($this->services[$name])) {
            if (get_class($this->services[$name]) === $className) {
                return;
            }
            throw new ServiceAlreadyExistException($name);
        }

        $this->services[$name] = new $className();
    }

    protected function __clone() {
    }
}
