<?php

namespace Oforge\Engine\Crud\Traits\Controller;

use Exception;
use Oforge\Engine\Core\Abstracts\AbstractModel;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Crud\Enums\CrudDataAccessLevel;
use Oforge\Engine\Crud\Services\GenericCrudService;

/**
 * Trait CrudBaseTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudBaseTrait {
    /** @var string $model */
    protected $model = null;
    /**
     * Define model properties:
     *      $this->modelProperties = [
     *          <propertyPath:dot-syntax> => [
     *              'type'   => CrudDataType::...|<Custom>, // Required. If Custom, the type should be unique and we recommend type = '<Engine|Module|Plugin-Name><ModelName><PropertyName>'
     *              'mode'   => CrudDataMode::..., // Required of not type=ID
     *              'label'  => 'i18n-translated-text' | ['key' => 'label_id', 'default' => 'ID'],
     *              'config' => [
     *                  'hint'        => 'i18n-translated-text'| ['key' => 'label_id', 'default' => 'ID'], // Hint text.(Optional)
     *                  'placeholder' => 'i18n-translated-text'| ['key' => 'label_id', 'default' => 'ID'], // (Optional)
     *                  'width'       => 200,   // if type = image (Optional)
     *                  'default'     => '',    // Default value. (Optional)
     *                  'required'    => false, // (Optional)
     *                  'pattern'     => '...', // If type = string|text. (Optional)
     *                  'maxlength'   => ...,   // If type = string|text. (Optional)
     *                  'min'         => '...', // If type = int|float. (Optional)
     *                  'max'         => ...,   // If type = int|float. (Optional)
     *                  'step'        => ...,   // If type = int|float. (Optional)
     *                  'multiple'    => false, // If type = select. (Optional)
     *                  'size'        => ...,   // If type = select. (Optional)
     *                  // Select with optgroups for type=select
     *                  'list' => 'functionName' | [
     *                      'key'   => [
     *                          'label'     => 'i18n-translated-text',
     *                          'options'   => [
     *                              'value' => => 'i18n-translated-text',
     *                          ]
     *                      ],
     *                  ],
     *                  // Simple select for type=select or optional as datalist for type=string
     *                  'list' => 'functionName' | [
     *                      'value' => 'i18n-translated-text', # Simple select
     *                  ],
     *              ],
     *          ],
     *      ];
     *
     * @var array $model
     */
    protected $modelProperties = [];
    /**
     * If enabled, the data output is limited to the properties defined in modelProperties.
     *
     * @var bool $strictReadMode
     */
    protected $strictReadMode = true;
    /**
     * If enabled, only the properties defined in modelProperties with mode=create|update are taken over when creating and updating.
     *
     * @var bool $strictWriteMode
     */
    protected $strictWriteMode = true;
    /** @var GenericCrudService $crudService */
    protected $crudService;
    /** @var string $modelName */
    protected $modelName;

    /**
     * @throws ServiceNotFoundException
     */
    protected function __constructCrudBaseTrait() {
        if ($this->model === null || empty($this->modelProperties)) {
            echo 'Properties "$model" and "$modelProperties" must be overridden!';
            die();
        }
        $this->crudService = Oforge()->Services()->get('crud');
        $this->modelName   = '';
        // Oforge\<Engine|Modules|Plugins>\<ExtensionName>\Models\<ModelName>
        if (isset($this->model)) {
            $parts = explode('\\', $this->model);
            if (count($parts) > 4) {
                unset($parts[0], $parts[3]);
                $this->modelName = implode('_', $parts);
            }
        }
        $this->modelProperties = $this->runtimeAdjustedModelProperties($this->modelProperties);
    }

    /**
     * Process input data and convert to model data, e.g. string to DateTime or id to entity.
     *
     * @param array $data
     * @param string $context Crud action or custom contexts.
     *
     * @return array
     * @throws Exception
     */
    protected function processInputData(array $data, string $context) : array {
        $accessLevel = ($context === 'update' ? CrudDataAccessLevel::UPDATE : CrudDataAccessLevel::CREATE);

        $data = $this->filterArrayByModelPropertyMode($data, $this->strictWriteMode, $accessLevel, $context);
        if (method_exists($this, 'processInputDataConvertDateTimeFromString')) {
            $this->processInputDataConvertDateTimeFromString($data);
        }

        return $data;
    }

    /**
     * Prepare item data for output, e.G. DateTime to string or additional custom data.
     *
     * @param AbstractModel|null $entity
     * @param string $context Crud action or custom contexts.
     *
     * @return array
     * @throws Exception
     */
    protected function prepareOutputData(?AbstractModel $entity, string $context) : array {
        $data = $entity === null ? [] : $entity->toArray();

        $data = $this->filterArrayByModelPropertyMode($data, $this->strictReadMode, CrudDataAccessLevel::READ, $context);
        if (method_exists($this, 'prepareOutputDataConvertDateTime2String')) {
            $this->prepareOutputDataConvertDateTime2String($data);
        }

        return $data;
    }

    /**
     * @param array $array Input array
     * @param bool $strictMode
     * @param int $accessLevel Minimal access level (CrudDataAccessLevel::...).
     * @param string $context
     *
     * @return array
     * @see CrudDataAccessLevel
     */
    protected function filterArrayByModelPropertyMode(array $array, bool $strictMode, int $accessLevel, string $context) : array {
        $result = $array;
        if ($strictMode) {
            $result = [];
            foreach ($this->modelProperties as $property => $propertyConfig) {
                // if ($context !== 'index' && $property ==='id' && ArrayHelper::get($propertyConfig, 'type') === CrudDataType::ID) {
                //     continue;// hide id property
                // }
                if (ArrayHelper::get($propertyConfig, 'mode', CrudDataAccessLevel::OFF) < $accessLevel) {
                    continue;
                }
                if (!ArrayHelper::dotExist($array, $property)) {
                    continue;
                }
                $value  = ArrayHelper::dotGet($array, $property, null);
                $result = ArrayHelper::dotSet($result, $property, $value);
            }
        }

        return $result;
    }

    /**
     * Adjustments of modelProperties config at runtime, e.g. for adding options by configs.
     *
     * @param array $modelProperties
     *
     * @return array
     */
    protected function runtimeAdjustedModelProperties(array $modelProperties) {
        return $modelProperties;
    }

    /**
     * Prepare crud meta with properties & if available filter.
     *
     * @return array[]
     */
    protected function prepareCrudMeta() : array {
        $listCache = [];
        $data      = [
            'properties' => [],
        ];
        foreach ($this->modelProperties as $property => $propertyConfig) {
            if (isset($propertyConfig['list'])) {
                $tmp = $propertyConfig['list'];
                if (is_callable($tmp)) {
                    $propertyConfig['list'] = $tmp();
                } elseif (is_string($tmp) && is_callable($callable = [$this, $tmp])) {
                    if (!isset($listCache[$tmp])) {
                        $listCache[$tmp] = $callable();
                    }
                    $propertyConfig['list'] = $listCache[$tmp];
                }
            }
            if (isset($propertyConfig['config'])) {
                $this->replaceI18NConfig($propertyConfig['config'], ['hint', 'placeholder']);
            }
            $data['properties'][$property] = $propertyConfig;
        }
        if (isset($this->indexFilter)) {
            $indexFilter    = $this->indexFilter;
            $data['filter'] = [];
            foreach ($indexFilter as $property => $filterConfig) {
                $this->replaceI18NConfig($filterConfig, ['label']);
                $data['filter'][$property] = $filterConfig;
            }
        }

        return $data;
    }

    /**
     * @param array $array
     * @param string[] $keys
     */
    protected function replaceI18NConfig(array &$array, $keys) {
        foreach ($keys as $key) {
            if (isset($array[$key]) && is_array($array[$key])) {
                //TODO after i18n module added
                // $array[$key] = I18N::translate($array[$key])
            }
        }
    }

    /**
     * @param callable $callable With params $property, $propertyConfig
     */
    protected function iterateModelProperties(callable $callable) {
        $modelProperties = $this->modelProperties;
        if (empty(!$modelProperties)) {
            foreach ($modelProperties as $property => $propertyConfig) {
                $callable($property, $propertyConfig);
            }
        }
    }

}
