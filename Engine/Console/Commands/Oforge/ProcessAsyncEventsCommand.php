<?php

namespace Oforge\Engine\Console\Commands\Oforge;

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
        'name'        => 'oforge:processAsyncEvents',
        'description' => 'Async events processing',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('Start async event processing', OutputInterface::VERBOSITY_DEBUG);
        Oforge()->Events()->processAsyncEvents();
        $output->writeln('Finished', OutputInterface::VERBOSITY_DEBUG);

        return self::SUCCESS;
    }
}
