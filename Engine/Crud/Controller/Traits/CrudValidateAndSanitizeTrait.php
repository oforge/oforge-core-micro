<?php

namespace Oforge\Engine\Crud\Controller\Traits;

use Exception;
use Oforge\Engine\Core\Helper\ArrayHelper;
use Oforge\Engine\Crud\Enum\CrudDataType;

/**
 * Trait CrudValidateAndSanitizeTrait
 *
 * @package Oforge\Engine\Crud\Controller\Traits
 */
trait CrudValidateAndSanitizeTrait {

    /**
     * @param array $data
     *
     * @throws Exception
     */
    protected function validateAndSanitizeEntityData(array &$data) {
        $modelProperties = $this->modelProperties;
        if (empty(!$modelProperties)) {
            foreach ($modelProperties as $property => $propertyConfig) {
                if (isset($data[$property])) {
                    $customCallable  = ArrayHelper::get($propertyConfig, 'validateAndSanitize', null);
                    $data[$property] = $this->validateAndSanitizeEntityDatum($data[$property], $property, $propertyConfig['type'], $customCallable);
                }
            }
        }
    }

    /**
     * @param mixed $valueRaw
     * @param string $property
     * @param string $dataType
     * @param callable|null $customCallable
     *
     * @return mixed
     * @throws Exception
     * @dev
     */
    protected function validateAndSanitizeEntityDatum($valueRaw, string $property, string $dataType, ?callable $customCallable) {
        // $exception = new InvalidDataTypeException("Property '$property' is not of type $dataType.");
        $exception = new Exception("Property '$property' is not of type $dataType.");
        if ($customCallable !== null) {
            return call_user_func($customCallable, $valueRaw, $property, $exception);
        }
        switch ($dataType) {
            // case CrudDataType::ID:
            // case CrudDataType::FILE:
            //     if (!is_int($valueRaw) && !is_string($valueRaw)) {
            //         throw $exception;
            //     }
            //
            //     return $valueRaw;
            case CrudDataType::BOOL:
                $value = filter_var($valueRaw, FILTER_VALIDATE_BOOLEAN | FILTER_NULL_ON_FAILURE);
                if ($value === null) {
                    throw $exception;
                }

                return $value;
            // case CrudDataType::COLOR:
            // case CrudDataType::DATE:
            // case CrudDataType::DATETIME:
            // case CrudDataType::TIME:
            case CrudDataType::FLOAT:
                if (is_float($valueRaw)) {
                    return $valueRaw;
                }
                throw $exception;
            case CrudDataType::INT:
                if (is_int($valueRaw)) {
                    return $valueRaw;
                }
                throw $exception;
            case CrudDataType::HTML:
            case CrudDataType::TEXT:
            case CrudDataType::STRING:
                if (is_string($valueRaw)) {
                    return $valueRaw;
                }
                throw $exception;
            case CrudDataType::EMAIL:
                if (filter_var($valueRaw, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE)) {
                    return $valueRaw;
                }
                throw $exception;
            // case CrudDataType::URL:
            //     if (filter_var($valueRaw, FILTER_VALIDATE_URL)) {
            //         return $valueRaw;
            //     }
            //     throw $exception;
            // no validation and sanitizing
            // case CrudDataType::SELECT:
            default:
                return $valueRaw;
        }
    }

}
