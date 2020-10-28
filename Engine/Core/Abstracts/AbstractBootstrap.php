<?php

namespace Oforge\Engine\Core\Abstracts;

use Exception;
use Oforge\Engine\Core\Helper\Statics;

/**
 * Class AbstractBootstrap
 * Specific Bootstrap classes are needed to either autoload Modules or Plugins
 *
 * @package Oforge\Engine\Core\Abstracts
 */
abstract class AbstractBootstrap {
    /**
     * @var string[] $cronjobs
     */
    protected $cronjobs = [];
    /**
     * @var string[] $dependencies
     */
    protected $dependencies = [];
    /**
     * @var string[] $endpoints
     */
    protected $endpoints = [];
    /**
     * @var array $middlewares
     */
    protected $middlewares = [];
    /**
     * @var string[] $models
     */
    protected $models = [];
    /**
     * @var array $services
     */
    protected $services = [];
    /**
     * @var int $order
     */
    protected $order = Statics::DEFAULT_ORDER;
    /** @var array $configuration */
    private $configuration = [];

    /** @throws Exception */
    public function install() {
    }

    public function update() {
    }

    /**
     * @param bool $keepData
     *
     * @throws Exception
     */
    public function uninstall(bool $keepData) {
    }

    /** @throws Exception */
    public function activate() {
    }

    /** @throws Exception */
    public function deactivate() {
    }

    /** @throws Exception */
    public function load() {
    }

    /**
     * @return string[]
     */
    public function getCronjobs() {
        return $this->cronjobs;
    }

    /**
     * @return string[]
     */
    public function getDependencies() {
        return $this->dependencies;
    }

    /**
     * @return array
     */
    public function getEndpoints() : array {
        return $this->endpoints;
    }

    /**
     * @return array
     */
    public function getMiddlewares() : array {
        return $this->middlewares;
    }

    /**
     * @return string[]
     */
    public function getModels() {
        return $this->models;
    }

    /**
     * @return array
     */
    public function getServices() : array {
        return $this->services;
    }

    /**
     * @return int
     */
    public function getOrder() : int {
        return $this->order;
    }

    /**
     * @param string $context
     *
     * @return array
     */
    public function getConfiguration(string $context) : array {
        return isset($this->configuration[$context]) ? $this->configuration[$context] : [];
    }

    /**
     * @param string $context
     * @param array $configuration
     */
    protected function setConfiguration(string $context, array $configuration) {
        $this->configuration[$context] = $configuration;
    }

}
