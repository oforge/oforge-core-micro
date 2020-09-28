<?php

namespace Oforge\Engine\Console\Commands\Core;

use Monolog\Logger;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Console\Lib\Input;

/**
 * Class ProcessAsyncEventsCommand
 *
 * @package Oforge\Engine\Console\Commands\Core
 */
class ProcessAsyncEventsCommand extends AbstractCommand {

    /**
     * PingCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:events:process-async', self::TYPE_DEFAULT);
        $this->setDescription('Async events processing');
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $output->info('Start async event processing');
        Oforge()->Events()->processAsyncEvents();
        $output->info('Finished');
    }

}
