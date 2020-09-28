<?php

namespace Oforge\Engine\Console\Commands\Example;

use Monolog\Logger;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Console\Lib\Input;

/**
 * Class ExampleCommandTwo
 *
 * @package Oforge\Engine\Console\Commands\Development\Example
 */
class ExampleCommandThree extends AbstractCommand {

    /**
     * ExampleCommandTwo constructor.
     */
    public function __construct() {
        parent::__construct('example:cmd3', self::TYPE_DEVELOPMENT);
        $this->setDescription('Example command 3');
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $output->notice(ExampleCommandThree::class);
    }

}
