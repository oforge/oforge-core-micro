<?php

namespace Oforge\Engine\Console\Commands\Oforge;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Services\PingService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PingCommand
 *
 * @package Oforge\Engine\Console\Commands\Oforge
 */
class PingCommand extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'oforge:ping',
        'description' => 'Ping function to test system.',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        /** @var PingService $pingService */
        $pingService = Oforge()->Services()->get('ping');
        $output->writeln($pingService->me());

        return self::SUCCESS;
    }

}
