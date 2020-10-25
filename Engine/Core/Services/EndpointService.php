<?php

namespace Oforge\Engine\Core\Services;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Annotation\Endpoint\AssetBundleMode;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\Core\Helper\StringHelper;
use Oforge\Engine\Core\Models\Endpoint\Endpoint as EndpointModel;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class EndpointService
 *
 * @package Oforge\Engine\Core\Services
 */
class EndpointService extends AbstractDatabaseAccess {
    /** @var array $configCache */
    private $configCache = [];
    /** @var EndpointModel[] */
    private $activeEndpoints;

    public function __construct() {
        parent::__construct(EndpointModel::class);
    }

    /**
     * @return EndpointModel[]
     * @throws ORMException
     */
    public function getActiveEndpoints() {
        if (!isset($this->activeEndpoints)) {
            $this->activeEndpoints = $this->repository()->findBy(['active' => 1], ['order' => 'ASC']);
        }

        return $this->activeEndpoints;
    }

    /**
     * Store endpoints in a database table
     *
     * @param array $endpoints
     *
     * @throws AnnotationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function install(array $endpoints) {
        $endpointConfigs = $this->prepareEndpointConfigs($endpoints);

        $created = false;
        foreach ($endpointConfigs as $endpointConfig) {
            /** @var EndpointModel $endpoint */
            $endpoint = $this->repository()->findOneBy(['name' => $endpointConfig['name']]);
            if (!isset($endpoint)) {
                $endpoint = EndpointModel::create($endpointConfig);
                $this->entityManager()->create($endpoint, false);
                $created = true;
            }
        }

        if ($created) {
            $this->entityManager()->flush();
            $this->repository()->clear();
        }
    }

    /**
     * Activation of endpoints.
     *
     * @param array $endpoints
     *
     * @throws AnnotationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function activate(array $endpoints) {
        $this->iterateEndpointModels($endpoints, function (EndpointModel $endpoint) {
            $endpoint->setActive(true);
        });
    }

    /**
     * Deactivation of endpoints.
     *
     * @param array $endpoints
     *
     * @throws AnnotationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deactivate(array $endpoints) {
        $this->iterateEndpointModels($endpoints, function (EndpointModel $endpoint) {
            $endpoint->setActive(false);
        });
    }

    /**
     * Removing endpoints
     *
     * @param array $endpoints
     *
     * @throws AnnotationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deinstall(array $endpoints) {
        $this->iterateEndpointModels($endpoints, function (EndpointModel $endpoint) {
            $this->entityManager()->remove($endpoint, false);

            return true;
        });
    }

    /**
     * @param array $endpoints
     * @param callable $callable
     *
     * @throws AnnotationException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function iterateEndpointModels(array $endpoints, callable $callable) {
        $endpointConfigs = $this->prepareEndpointConfigs($endpoints);

        foreach ($endpointConfigs as $endpointConfig) {
            /** @var EndpointModel[] $endpoints */
            $endpoints = $this->repository()->findBy(['name' => $endpointConfig['name']]);

            if (!empty($endpoints)) {
                foreach ($endpoints as $endpoint) {
                    $callable($endpoint);
                    $this->entityManager()->update($endpoint, false);
                }
                $this->entityManager()->flush();
            }
        }
        $this->repository()->clear();
    }

    /**
     * @param array $endpoints
     *
     * @return array
     * @throws AnnotationException
     */
    protected function prepareEndpointConfigs(array $endpoints) : array {
        $isProductionMode = Oforge()->Settings()->isProductionMode();
        $endpointConfigs  = [];
        class_exists(EndpointClass::class);
        class_exists(EndpointAction::class);

        if (!file_exists(ROOT_PATH . Statics::DIR_CACHE_ENDPOINT)) {
            FileSystemHelper::mkdir(ROOT_PATH . Statics::DIR_CACHE_ENDPOINT);
        }

        $reader = new IndexedReader(new AnnotationReader());
        // $isDevMode = false;
        // $cacheDir  = ROOT_PATH . Statics::GLOBAL_SEPARATOR . Statics::CACHE_DIR . Statics::GLOBAL_SEPARATOR . 'endpoint';
        // $filesystemCache = new FilesystemCache($cacheDir);
        // $reader    = new CachedReader($reader, $filesystemCache, $isDevMode);

        foreach ($endpoints as $class) {
            if (!is_string($class)) {
                continue;
            }
            $fileName = $class;
            if (StringHelper::startsWith($fileName, 'Oforge')) {
                $fileName = explode('Oforge', $class, 2)[1];
            }
            $fileName = ltrim(str_replace('\\', '_', $fileName), '_');
            if ($isProductionMode) {
                $cacheFile = ROOT_PATH . Statics::DIR_CACHE_ENDPOINT . Statics::GLOBAL_SEPARATOR . $fileName . '.cache';
                if (file_exists($cacheFile)) {
                    if (!isset($this->configCache[$fileName])) {
                        $content                      = trim(file_get_contents($cacheFile));
                        $this->configCache[$fileName] = unserialize($content);
                    }
                    $endpointConfigsForClass = $this->configCache[$fileName];
                } else {
                    $endpointConfigsForClass      = $this->getEndpointConfigFromClass($reader, $class);
                    $this->configCache[$fileName] = $endpointConfigsForClass;
                    file_put_contents($cacheFile, serialize($endpointConfigsForClass));
                }
            } else {
                if (!isset($this->configCache[$fileName])) {
                    $this->configCache[$fileName] = $this->getEndpointConfigFromClass($reader, $class);
                }
                $endpointConfigsForClass = $this->configCache[$fileName];
            }
            if (empty($endpointConfigsForClass)) {
                Oforge()->Logger()->get()->addWarning("An endpoint was defined but the corresponding controller '$class' has no action methods.");
            } else {
                $endpointConfigs = array_merge($endpointConfigs, $endpointConfigsForClass);
            }
        }
        if (!empty($endpoints) && empty($endpointConfigs)) {
            Oforge()->Logger()->get()->addWarning('Endpoints were defined but the corresponding controllers has no action methods.', $endpoints);
        }

        return $endpointConfigs;
    }

    /**
     * Extract endpoint config from Class.
     *
     * @param Reader $reader
     * @param string $class
     *
     * @return array
     * @throws AnnotationException
     */
    protected function getEndpointConfigFromClass(Reader $reader, string $class) {
        $endpointConfigs = [];
        try {
            $reflectionClass = new ReflectionClass($class);
            /** @var EndpointClass $classAnnotation */
            $classAnnotation = $reader->getClassAnnotation($reflectionClass, EndpointClass::class);
            if ($classAnnotation === null) {
                Oforge()->Logger()->get()
                        ->addWarning("An endpoint was defined but the corresponding controller '$class' has no configurated annotation 'EndpointClass'.");

                return $endpointConfigs;
            }
            $classAnnotation->checkRequired($class);

            $classMethods = get_class_methods($class);
            if ($classMethods === null) {
                Oforge()->Logger()->get()->addWarning("Get class methods failed for '$class'. Maybe some namespace, class or method was defined wrong.");
                $classMethods = [];
            }
            foreach ($classMethods as $classMethod) {
                $isMethodActionPrefix = StringHelper::endsWith($classMethod, 'Action');
                if ($classAnnotation->isStrictActionSuffix() && !$isMethodActionPrefix) {
                    continue;
                }
                $reflectionMethod = new ReflectionMethod($class, $classMethod);
                /** @var EndpointAction $methodAnnotation */
                $methodAnnotation = $reader->getMethodAnnotation($reflectionMethod, EndpointAction::class);
                if (!$classAnnotation->isStrictActionSuffix() && is_null($methodAnnotation)) {
                    // skipping of methods without endpoint action annotation in disabled strict mode
                    continue;
                }
                if (isset($methodAnnotation) && !$methodAnnotation->isCreate()) {
                    continue;
                }
                $endpointConfig = $this->buildEndpointConfig($class, $classMethod, $isMethodActionPrefix, $classAnnotation, $methodAnnotation);
                if (!empty($endpointConfig)) {
                    $endpointConfigs[] = $endpointConfig;
                }
            }
        } catch (ReflectionException $exception) {
            Oforge()->Logger()->get()->addWarning('Reflection exception: ' . $exception->getMessage(), $exception->getTrace());
        }

        return $endpointConfigs;
    }

    /**
     * @param string $class
     * @param string $classMethod
     * @param bool $isMethodActionPrefix
     * @param EndpointClass $classAnnotation
     * @param EndpointAction $methodAnnotation
     *
     * @return array
     */
    protected function buildEndpointConfig(
        string $class,
        string $classMethod,
        bool $isMethodActionPrefix,
        EndpointClass $classAnnotation,
        ?EndpointAction $methodAnnotation
    ) : array {
        /** @var string|string[]|null $assetBundles */
        $parentName = $classAnnotation->getName();
        $name       = $parentName . '_';
        $path       = $classAnnotation->getPath();
        $order      = null;
        $httpMethod = EndpointMethod::ANY;

        $AssetBundleMode   = $classAnnotation->getAssetBundleMode();
        $classAssetBundles  = $classAnnotation->getAssetBundles();
        $methodAssetBundles = null;

        $context = explode('\\', $class)[StringHelper::startsWith($class, 'Oforge') ? 3 : 0];

        $actionName = $classMethod;
        if ($isMethodActionPrefix) {
            $actionName = explode('Action', $actionName)[0];
        }
        $isIndexAction = ($actionName === 'index');

        if (isset($methodAnnotation)) {
            $order              = $methodAnnotation->getOrder();
            $methodAssetBundles = $methodAnnotation->getAssetBundles();
            $AssetBundleMode   = $methodAnnotation->getAssetBundleMode() ?? $AssetBundleMode;
            if (EndpointMethod::isValid($methodAnnotation->getMethod())) {
                $httpMethod = $methodAnnotation->getMethod();
            }
        }
        if (isset($methodAnnotation) && $methodAnnotation->getPath() !== null) {
            $path .= $methodAnnotation->getPath();
        } elseif (!$isIndexAction) {
            $path .= '/' . $actionName;
        }
        if (isset($methodAnnotation) && !empty($methodAnnotation->getName())) {
            $name .= $methodAnnotation->getName();
        } elseif (!$isIndexAction) {
            $name .= $actionName;
        }

        $name  = trim($name, '_');
        $path  = StringHelper::leading($path, '/');
        $order = $order ?? $classAnnotation->getOrder() ?? Statics::DEFAULT_ORDER;

        $AssetBundleMode = $AssetBundleMode ?? AssetBundleMode::OVERRIDE;
        $assetBundles     = $this->prepareEndpointAssetBundles($AssetBundleMode, $classAssetBundles, $methodAssetBundles);

        return [
            'name'             => $name,
            'parentName'       => $parentName,
            'path'             => $path,
            'context'          => $context,
            'controllerClass'  => $class,
            'controllerMethod' => $classMethod,
            'assetBundles'     => $assetBundles,
            'httpMethod'       => $httpMethod,
            'order'            => $order,
            // 'controllerAction' => $actionName ?? '-',
        ];
    }

    /**
     * @param string $AssetBundleMode
     * @param string|string[]|null $classAssetBundles
     * @param string|string[]|null $methodAssetBundles
     *
     * @return string[]
     */
    private function prepareEndpointAssetBundles(string $AssetBundleMode, $classAssetBundles, $methodAssetBundles) {
        /**
         * @param string|string[]|null $values
         *
         * @return string[]
         */
        $convert = function ($values) {
            if ($values === null) {
                return [];
            }
            if (is_string($values)) {
                $values = explode(',', $values);
            }

            return $values;
        };

        switch ($AssetBundleMode) {
            case AssetBundleMode::MERGE:
                $assetBundles = array_unique(array_merge($convert($classAssetBundles), $convert($methodAssetBundles)));
                break;
            case AssetBundleMode::NONE:
                $assetBundles = [];
                break;
            case AssetBundleMode::OVERRIDE:
            default:
                $assetBundles = $convert($methodAssetBundles);
                if (empty($assetBundles)) {
                    $assetBundles = $convert($classAssetBundles);
                }
        }
        if (is_string($assetBundles)) {
            $assetBundles = explode(',', $assetBundles);
        }
        foreach ($assetBundles as $index => $assetBundle) {
            $assetBundles[$index] = ucfirst(trim($assetBundle));
        }

        return $assetBundles;
    }

}
