<?php

namespace Oforge\Engine\Core\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Core\Exceptions\DependencyNotResolvedException;
use Oforge\Engine\Core\Models\Plugin\Plugin;
use function PHPSTORM_META\map;

class PluginAccessService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => Plugin::class]);
    }

    /**
     * @return array|object[]
     * @throws ORMException
     * @throws DependencyNotResolvedException
     */
    public function getActive() {
        //find all plugins order by "order"
        /** @var Plugin[] $plugins */
        $plugins = $this->repository()->findBy(["active" => 1], ['order' => 'ASC']);
        //create working bucket with all plugins that should be started

        $dependencyList = [];

        foreach ($plugins as $plugin) {
            $classname    = $plugin->getName() . "\\Bootstrap";
            $instance     = Oforge()->getBootstrapManager()->getBootstrapInstance($classname);
            $dependencies = array_map(function ($val) {
                return explode('\\', $val)[0];
            }, $instance->getDependencies());

            $dependencyList[] = [
                'name'         => $plugin->getName(),
                'dependencies' => $dependencies,
            ];
        }

        $result = [];
        $checkedDependencies = [];

        // while not all items are resolved:
        while(count($dependencyList) > count($result)) {
            $success = false;
            foreach($dependencyList as $plugin) {
                if(isset($checkedDependencies[$plugin['name']])) {
                    continue;
                }
                $resolved = true;
                if(isset($plugin['dependencies'])) {
                    foreach($plugin['dependencies'] as $dependency) {
                        if(!isset($checkedDependencies[$dependency])) {
                            // there is a dependency that is not met:
                            $resolved = false;
                            break;
                        }
                    }
                }
                if($resolved) {
                    //all dependencies are met:
                    $checkedDependencies[$plugin['name']] = true;
                    $result[] = $plugin;
                    $success = true;
                }
            }

            if(!$success) {
                echo ('Dependency for ' . $dependency . ' could not be resolved.');
                throw new DependencyNotResolvedException($dependency);
            }
        }

        return array_reverse($result);
    }

}
