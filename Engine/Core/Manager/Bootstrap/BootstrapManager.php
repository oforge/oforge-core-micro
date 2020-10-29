<?php

namespace Oforge\Engine\Core\Manager\Bootstrap;

use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Bootstrap as CoreBootstrap;
use Oforge\Engine\Core\Helper\ArrayPhpFileStorage;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Oforge\Engine\Core\Helper\StringHelper;
use Oforge\Engine\TemplateEngine\Core\Abstracts\AbstractTemplate;

/**
 * Class BootstrapManager
 *
 * @package Oforge\Engine\Core\Manager\BootstrapManager
 */
class BootstrapManager
{
    private const FILE_PATH = ROOT_PATH . Statics::DIR_CACHE . Statics::GLOBAL_SEPARATOR . 'bootstrap.cache.php';
    public const  KEY_PATH = 'path';
    public const  KEY_NS = 'namespace';
    /** @var BootstrapManager $instance */
    protected static $instance = null;
    /** @var array $bootstrapData */
    private $bootstrapData = [];
    /** @var array $bootstrapInstances s */
    private $bootstrapInstances = [];

    protected function __construct()
    {
    }

    /**
     * @return BootstrapManager
     */
    public static function getInstance(): BootstrapManager
    {
        if (is_null(self::$instance)) {
            self::$instance = new BootstrapManager();
        }

        return self::$instance;
    }

    /**
     * @param string $class
     *
     * @return AbstractBootstrap|null
     */
    public function getBootstrapInstance(string $class): ?AbstractBootstrap
    {
        if (isset($this->bootstrapInstances[$class])) {
            return $this->bootstrapInstances[$class];
        }

        return null;
    }

    /**
     * Returns map of bootstrap instances with bootstrap class key.
     *
     * @return array
     */
    public function getBootstrapInstances(): array
    {
        return $this->bootstrapInstances;
    }

    /**
     * Returns module bootstrap data.
     *
     * @return mixed
     */
    public function getModuleBootstrapData()
    {
        return $this->bootstrapData[Statics::ENGINE_DIR];
    }

    /**
     * Returns plugin bootstrap data.
     *
     * @return mixed
     */
    public function getPluginBootstrapData()
    {
        return $this->bootstrapData[Statics::PLUGIN_DIR];
    }


    /**
     * Initialize all modules and plugins bootstrap data.
     */
    public function init()
    {
        $isDevelopmentMode = Oforge()->Settings()->isDevelopmentMode();
        if ($isDevelopmentMode) {
            $this->collectBootstrapData();
            // $this->updateBootstrapData();
        } else {
            if (file_exists(self::FILE_PATH)) {
                $this->bootstrapData = ArrayPhpFileStorage::load(self::FILE_PATH);

                $update = false;
                foreach ($this->bootstrapData as $type => $array) {
                    foreach ($array as $bootstrapClass => $bootstrapData) {
                        if (!file_exists($bootstrapData[self::KEY_PATH])) {
                            $update = true;
                        }
                    }
                }
                if ($update) {
                    $this->updateBootstrapData();
                }
            } else {
                $this->updateBootstrapData();
            }
        }
        foreach ($this->bootstrapData as $type => $data) {
            foreach ($data as $bootstrapClass => $bootstrapData) {
                if (is_subclass_of($bootstrapClass, AbstractBootstrap::class)) {
                    /** @var AbstractBootstrap $instance */
                    $instance = new $bootstrapClass();
                    if ($bootstrapClass === CoreBootstrap::class) {
                        Oforge()->DB()->initModelSchema($instance->getModels());
                    }
                    $this->bootstrapInstances[$bootstrapClass] = $instance;
                } elseif (is_subclass_of($bootstrapClass, AbstractTemplate::class)) {
                    /** @var AbstractTemplate $instance */
                    $instance = new $bootstrapClass();

                    $this->bootstrapInstances[$bootstrapClass] = $instance;
                }
            }
        }
    }

    /**
     * Create parent folder if not exist and updates Bootstrap-data file.
     */
    public function updateBootstrapData()
    {
        $this->collectBootstrapData();

        if (!file_exists($dir = dirname(self::FILE_PATH))) {
            @mkdir($dir, 0755, true);
        }

        if (!ArrayPhpFileStorage::write(self::FILE_PATH, $this->bootstrapData)) {
            Oforge()->Logger()->get()->emergency('Couldn\'t write bootstrap collection file: ' . self::FILE_PATH);
        }
    }

    /**
     * Collect and set all Bootstrap-data of modules and plugins.
     */
    protected function collectBootstrapData()
    {
        $bootstrapData = [
            Statics::ENGINE_DIR => $this->collectBootstrapDataSub(Statics::ENGINE_DIR),
            Statics::PLUGIN_DIR => $this->collectBootstrapDataSub(Statics::PLUGIN_DIR),
        ];

        $this->bootstrapData = $bootstrapData;
    }

    /**
     * Collect and return all Bootstrap-files modules or plugins.
     *
     * @param string $context Context of search.
     *
     * @return array
     */
    protected function collectBootstrapDataSub(string $context)
    {
        $isModule = $context === Statics::ENGINE_DIR;
        $isPlugin = $context === Statics::PLUGIN_DIR;
        $data = [];
        $files = FileSystemHelper::getBootstrapFiles(ROOT_PATH . Statics::GLOBAL_SEPARATOR . $context);
        foreach ($files as $file) {
            $directory = dirname($file);

            $class = str_replace('/', '\\', str_replace(ROOT_PATH, '', $directory)) . '\Bootstrap';

            $class = 'Oforge'.$class;

            if ($isModule || $isPlugin) {
                $namespace = StringHelper::rightTrim($class, '\\Bootstrap');
            }
            $class = StringHelper::leftTrim($class, '\\');

            $data[$class] = [
                self::KEY_NS => $namespace,
                self::KEY_PATH => $directory,
            ];
        }
        if ($isModule) {
            // set CoreBootstrap as first entry
            $tmp = $data[CoreBootstrap::class];
            unset($data[CoreBootstrap::class]);
            $data = [CoreBootstrap::class => $tmp] + $data;
        }

        return $data;
    }

}
