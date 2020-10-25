<?php

namespace Oforge\Modules\CoreApi\Controllers\Api;

use Oforge\Engine\Core\Annotation\Endpoint\AssetBundleMode;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Core\Traits\Controller\TraitInitializer;
use Oforge\Engine\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Core\Models\Store\KeyValue;
use Oforge\Engine\Crud\Traits\Controller\CrudBaseTrait;
use Oforge\Engine\Crud\Traits\Controller\CrudBundleCrudActionsTrait;
use Oforge\Engine\Crud\Enums\CrudDataAccessLevel;
use Oforge\Engine\Crud\Enums\CrudDataType;

/**
 * Class KeyValueController
 *
 * @package Oforge\Modules\CoreApi\Controllers\Api
 * @EndpointClass(path="/api/storage", name="api_storage", assetBundles=null, assetBundlesMode=AssetBundleMode::NONE)
 */
class KeyValueController {
    use TraitInitializer, CrudBaseTrait, CrudBundleCrudActionsTrait;

    /** KeyValueController constructor. */
    public function __construct() {
        [AssetBundleMode::class, EndpointAction::class, EndpointMethod::class];// Required for imports in nested traits

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
