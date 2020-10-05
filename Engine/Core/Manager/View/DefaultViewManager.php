<?php

namespace Oforge\Engine\Core\Manager\View;

use Oforge\Engine\Core\Abstracts\AbstractViewManager;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ViewManager
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Core\Manager
 */
class DefaultViewManager extends AbstractViewManager {
    /** @var DefaultViewManager $instance */
    protected static $instance;
    /** @var array $data */
    private $data = [];

    /**
     * Create a singleton instance of the ViewManager
     *
     * @return DefaultViewManager
     */
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new DefaultViewManager();
        }

        return self::$instance;
    }

    /**
     * Assign Data from a Controller to a Template
     *
     * @param array $data
     *
     * @return DefaultViewManager
     */
    public function assign(array $data) {
        $data       = ArrayHelper::dotToNested($data);
        $this->data = ArrayHelper::mergeRecursive($this->data, $data);

        return $this;
    }

    /**
     * Fetch View Data. This function should be called from the route middleware
     * so that it can transport the data to the TemplateEngine
     *
     * @return array
     */
    public function fetch() {
        return $this->data;
    }

    /**
     * Get a specific key value from the viewData or $default if data with key does not exist.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null) {
        return ArrayHelper::dotGet($this->data, $key, $default);
    }

    /**
     * Exist non empty value with key?
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key) : bool {
        return isset($this->data[$key]) && !empty($this->data[$key]);
    }

    /**
     * @param string $key
     *
     * @return DefaultViewManager
     */
    public function delete(string $key) {
        unset($this->data[$key]);

        return $this;
    }

    public function render(Request $request, Response $response, array $data) {
        if ($response->getStatusCode() !== 200 || !empty($response->getBody()->getSize())) {
            return $response;
        }

        return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->withJson($data);
    }
}
