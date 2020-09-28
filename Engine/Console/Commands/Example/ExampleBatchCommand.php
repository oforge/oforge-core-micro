<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractBatchCommand;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;

/**
 * Class ExampleBatchCommand
 *
 * @package Oforge\Engine\Console\Commands\Development\Example
 */
class ExampleBatchCommand extends AbstractBatchCommand {

    /**
     * ExampleBatchCommand constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct('example', ['example:cmd2'], self::TYPE_DEVELOPMENT);
        $this->setDescription('Example batch command. Will run subcommand cmd1, cmd3 and group.');
    }

}
