<?php

namespace Oforge\Engine\Core\Helper;

/**
 * ArrayHelper
 *
 * @package Oforge\Engine\Core\Helper
 */
class ArrayHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Check when array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array) : bool {
        return ($array !== array_values($array));
    }

    /**
     * Check if key exist (isset) and value is not empty.
     *
     * @param array $array
     * @param mixed $key
     *
     * @return bool
     */
    public static function issetNotEmpty(array $array, $key) : bool {
        return isset($array[$key]) && $array[$key] !== '';
    }

    /**
     * Returns array value or default.
     * Check array key with isset.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function get(array $array, $key, $defaultValue = null) {
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }

    /**
     * Pop array value or default.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function pop(array &$array, $key, $defaultValue = null) {
        if (isset($array[$key])) {
            $value = $array[$key];
            unset($array[$key]);

            return $value;
        }

        return $defaultValue;
    }

    /**
     * Returns array value or default.
     * Check array key with array_key_exists.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function getNullable(array $array, $key, $defaultValue = null) {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    /**
     * Create array with given keys and default value, then merge values of $inputArray
     *
     * @param array $keys
     * @param array $inputArray
     * @param string $defaultValue
     *
     * @return array
     */
    public static function extractData(array $keys, array $inputArray, $defaultValue = '') : array {
        $tmp = array_fill_keys($keys, $defaultValue);

        return array_replace($tmp, array_intersect_key($inputArray, $tmp));
    }

    /**
     * Extract all data by key with original value.
     *
     * @param array $inputArray
     * @param array $keys
     *
     * @return array
     */
    public static function extractByKey(array $inputArray, array $keys) {
        return array_intersect_key($inputArray, array_flip($keys));
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param bool $overrideNumericIndices
     *
     * @return array
     */
    public static function mergeRecursive(array $array1, array $array2, bool $overrideNumericIndices = false) {
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key])) {
                if (is_string($key)) {
                    $array1[$key] = is_array($array1[$key]) ? self::mergeRecursive($array1[$key], $value, $overrideNumericIndices) : $value;
                } else {
                    $array1[] = $value;
                }
            } elseif (is_numeric($key)) {
                if (!isset($array1[$key]) || $overrideNumericIndices) {
                    $array1[$key] = $value;
                } else {
                    $array1[] = $value;
                }
            } else {
                $array1[$key] = $value;
            }
        }
        unset($value);

        return $array1;
    }

    /**
     * Check if value by key in dot notation exist.
     *
     * @param array $array
     * @param string $key
     *
     * @return bool
     */
    public static function dotExist(array $array, string $key) : bool {
        return self::dotGet($array, $key, null) === self::dotGet($array, $key, false);
    }

    /**
     * Get value by key in dot notation or $default if not exist.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function dotGet(array $array, string $key, $default = null) {
        if (empty($array) || empty($key)) {
            return $default;
        }
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);
            $tmp  = $array;
            foreach ($keys as $key) {
                if (!isset($tmp[$key])) {
                    return $default;
                }

                $tmp = $tmp[$key];
            }

            return $tmp;
        }

        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * Set value in array by key in dot notation (e.g. meta.rout.name).
     *
     * @param array $array
     * @param string|string[] $keyOrKeyPath
     * @param mixed $value
     * @param bool $overrideExistingValue Override existing value or create array if not array and append value.
     *
     * @return array
     */
    public static function dotSet(array $array, $keyOrKeyPath, $value, bool $overrideExistingValue = true) {
        if (is_string($keyOrKeyPath)) {
            $keyOrKeyPath = explode('.', $keyOrKeyPath);
        }
        $tmp = &$array;
        foreach ($keyOrKeyPath as $key) {
            if (!isset($tmp[$key])) {
                $tmp[$key] = [];
            } elseif (!is_array($tmp[$key])) {
                $tmp[$key] = [$tmp[$key]];
            }
            $tmp = &$tmp[$key];
        }
        if ($overrideExistingValue) {
            $tmp = $value;
        } else {
            if (!is_array($tmp)) {
                $tmp = [$tmp];
            }
            $tmp[] = $value;
        }

        return $array;
    }

    /**
     * @param array $array
     * @param string|string[] $keyOrKeyPath
     *
     * @return array
     */
    public static function dotUnset(array $array, $keyOrKeyPath) {
        if (is_string($keyOrKeyPath)) {
            $keyOrKeyPath = explode('.', $keyOrKeyPath);
        }
        $tmp     = &$array;
        $lastKey = array_pop($keyOrKeyPath);
        foreach ($keyOrKeyPath as $key) {
            if (!isset($tmp[$key]) || !is_array($tmp[$key])) {
                return $array;
            }
            $tmp = &$tmp[$key];
        }
        unset($tmp[$lastKey]);

        return $array;
    }

    /**
     * Convert keys in dot notation to nested array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function dotToNested(array $array) : array {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = self::dotToNested($value);
            }
            $tmp    = (strpos($key, '.') === false ? [$key => $value] : self::dotSet([], $key, $value));
            $result = self::mergeRecursive($result, $tmp);
        }

        return $result;
    }

    /**
     * Removes all unwanted array keys.
     *
     * @param array $keys
     * @param array $inputArray
     *
     * @return array
     */
    public static function filterByKeys(array $keys, array $inputArray) {
        $tmp = array_fill_keys($keys, 1);

        return array_filter($inputArray, function ($key) use ($tmp) {
            return isset($tmp[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }

}
