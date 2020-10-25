<?php

namespace Oforge\Engine\Core\Helper;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Oforge\Engine\Core\Services\ConfigService;

/**
 * Class DateTimeFormatter
 *
 * @package Oforge\Engine\Core\Helper
 */
class DateTimeFormatter {
    /** @var ConfigService $configService */
    private static $configService;

    /** Prevent instance. */
    public function __construct() {
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function date(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_date', 'd.m.Y');
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function datetime(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_datetime', 'd.m.Y H:i:s');
    }

    /**
     * @param DateTimeInterface|null $dateTimeObject
     *
     * @return string
     */
    public static function time(?DateTimeInterface $dateTimeObject) : string {
        return self::format($dateTimeObject, 'system_format_time', 'H:i:s');
    }

    /**
     * @param string $input
     *
     * @return DateTimeImmutable|null
     */
    public static function parseDate(string $input) : ?DateTimeImmutable {
        return self::parse($input, 'system_format_date', 'Y-m-d');
    }

    /**
     * @param string $input
     *
     * @return DateTimeImmutable|null
     */
    public static function parseDatetime(string $input) : ?DateTimeImmutable {
        return self::parse($input, 'system_format_datetime', 'Y-m-d H:i:s');
    }

    /**
     * @param string $input
     *
     * @return DateTimeImmutable|null
     */
    public static function parseTime(string $input) : ?DateTimeImmutable {
        return self::parse($input, 'system_format_time', 'H:i:s');
    }

    /**
     * @param string|null $input
     * @param string $configKey
     * @param string $defaultFormat
     *
     * @return DateTimeImmutable|null
     */
    protected static function parse(?string $input, string $configKey, string $defaultFormat) : ?DateTimeImmutable {
        if (empty($input)) {
            return null;
        }
        try {
            if (!isset(self::$configService)) {
                self::$configService = Oforge()->Services()->get('config');
            }
            $format = self::$configService->get($configKey);

        } catch (Exception $exception) {
            $format = $defaultFormat;
        }
        $result = DateTimeImmutable::createFromFormat($format, $input);

        return ($result === false ? null : $result);
    }

    /**
     * Format DateTimeObjects.
     *
     * @param DateTimeInterface|null $dateTimeObject
     * @param string $configKey
     * @param string $defaultFormat
     *
     * @return string
     */
    private static function format(?DateTimeInterface $dateTimeObject, string $configKey, string $defaultFormat) : string {
        if (!isset($dateTimeObject)) {
            return '';
        }
        try {
            if (!isset(self::$configService)) {
                self::$configService = Oforge()->Services()->get('config');
            }
            $format = self::$configService->get($configKey);

        } catch (Exception $exception) {
            $format = $defaultFormat;
        }

        return $dateTimeObject->format($format);
    }

}
