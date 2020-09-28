<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractGroupCommand;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;

/**
 * Class ExampleBatchCommand
 *
 * @package Oforge\Engine\Console\Commands\Development\Example
 */
class ExampleGroupCommand extends AbstractGroupCommand {

    /**
     * ExampleBatchCommand constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct('example:group', ['example:cmd2', 'example:cmd1' => '',], self::TYPE_DEVELOPMENT);
        $this->setDescription('Example group command. Will run subcommand cmd2 and cmd1.');
    }

}
