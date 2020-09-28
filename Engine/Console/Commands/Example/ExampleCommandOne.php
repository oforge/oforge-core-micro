<?php

namespace Oforge\Engine\Console\Commands\Example;

use Monolog\Logger;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Console\Lib\Input;

/**
 * Class ExampleCommandOne
 *
 * @package Oforge\Engine\Console\Commands\Development\Example
 */
class ExampleCommandOne extends AbstractCommand {

    /**
     * ExampleCommandOne constructor.
     */
    public function __construct() {
        parent::__construct('example:cmd1', self::TYPE_DEVELOPMENT);
        $this->setDescription('Example command 1');
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $output->notice(ExampleCommandOne::class);
    }

}
