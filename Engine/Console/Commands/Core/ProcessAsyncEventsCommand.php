<?php

namespace Oforge\Engine\Console\Commands\Core;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessAsyncEventsCommand
 *
 * @package Oforge\Engine\Console\Commands\Core
 */
class ProcessAsyncEventsCommand extends AbstractCommand {
    /** @var array $config */
    protected $config = [
        'name'        => 'oforge:events:process-async',
        'description' => 'Async events processing',
        'hidden'      => true,
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Start async event processing');
        Oforge()->Events()->processAsyncEvents();
        $output->writeln('Finished');

        return self::SUCCESS;
    }
}
