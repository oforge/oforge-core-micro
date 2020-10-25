<?php

namespace Oforge\Engine\Crud\Traits\Controller\Converter;

use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Core\Helper\DateTimeFormatter;
use Oforge\Engine\Crud\Enums\CrudDataType;

/**
 * Trait CrudDateTimePropertyConverterTrait
 *
 * @package Oforge\Engine\Crud\Traits\Controller
 */
trait CrudDateTimePropertyConverterTrait {

    /**
     * @param array $data
     */
    protected function processInputDataConvertDateTimeFromString(array &$data) {
        $map = [
            CrudDataType::DATE     => 'parseDate',
            CrudDataType::DATETIME => 'parseDatetime',
            CrudDataType::TIME     => 'parseTime',
        ];
        $this->iterateModelProperties(function ($property, $propertyConfig) use (&$data, $map) {
            if (ArrayHelper::dotExist($data, $property) && isset($map[$propertyConfig['type']])) {
                $method = $map[$propertyConfig['type']];
                $value  = DateTimeFormatter::$method(ArrayHelper::dotGet($data, $property));
                $data   = ArrayHelper::dotSet($data, $property, $value);
            }
        });
    }

    /**
     * @param array $data
     */
    protected function prepareOutputDataConvertDateTime2String(array &$data) {
        $map = [
            CrudDataType::DATE     => 'date',
            CrudDataType::DATETIME => 'datetime',
            CrudDataType::TIME     => 'time',
        ];
        $this->iterateModelProperties(function ($property, $propertyConfig) use (&$data, $map) {
            if (ArrayHelper::dotExist($data, $property) && isset($map[$propertyConfig['type']])) {
                $method = $map[$propertyConfig['type']];
                $value  = DateTimeFormatter::$method(ArrayHelper::dotGet($data, $property));
                $data   = ArrayHelper::dotSet($data, $property, $value);
            }
        });
    }

}
