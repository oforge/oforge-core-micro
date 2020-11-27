<?php

namespace Oforge\Engine\Console\Lib;

use Monolog\Formatter\LineFormatter;
use Monolog\Utils;

/**
 * Class ConsoleFormatter
 *
 * @package Oforge\Engine\Console\Lib
 */
class ConsoleFormatter extends LineFormatter {
    private const FORMAT = "[%datetime%] %level_name%: %message% %context% %extra%\n";

    /**
     * @inheritDoc
     */
    public function __construct() {
        parent::__construct(self::FORMAT);
        $this->ignoreEmptyContextAndExtra = true;
        $this->includeStacktraces         = true;
    }

}
