<?php

namespace Oforge\Modules\CoreApi\Controller\Api;

use Oforge\Engine\Core\Annotation\Endpoint\AssetBundlesMode;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Core\Controller\Traits\TraitInitializer;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Core\Models\Store\KeyValue;
use Oforge\Engine\Crud\Controller\Traits\CrudBaseTrait;
use Oforge\Engine\Crud\Controller\Traits\CrudBundleCrudActionsTrait;
use Oforge\Engine\Crud\Enum\CrudDataAccessLevel;
use Oforge\Engine\Crud\Enum\CrudDataType;

/**
 * Class KeyValueController
 *
 * @package Oforge\Engine\Crud\Test\Controller
 * @EndpointClass(path="/api/storage", name="api_storage", assetBundles=null, assetBundlesMode=AssetBundlesMode::NONE)
 */
class KeyValueController {
    use TraitInitializer, CrudBaseTrait, CrudBundleCrudActionsTrait;

    /** KeyValueController constructor. */
    public function __construct() {
        [AssetBundlesMode::class, EndpointAction::class, EndpointMethod::class];// Required for imports in nested traits

        $this->model = KeyValue::class;

        $this->modelProperties = [
            'id'    => [
                'type' => CrudDataType::ID,
                'mode' => CrudDataAccessLevel::READ,
            ],
            'name'  => [
                'type' => CrudDataType::STRING,
                'mode' => CrudDataAccessLevel::CREATE,
            ],
            'value' => [
                'type' => 'ModuleCoreApiKeyValueType',
                'mode' => CrudDataAccessLevel::UPDATE,
            ],
        ];

        $this->callTraitMethod('__construct');
    }

}
