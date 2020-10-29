<?php

namespace Oforge\Engine\Console\Commands\Cleanup;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Manager\Logger\LoggerManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LogFilesCommand
 *
 * @package Oforge\Engine\Console\Commands\Cleanup
 */
class LogFilesCommand extends AbstractCommand {
    /** @var array $config */
    protected $config = [
        'name'        => 'cleanup:logs',
        'description' => 'Cleanup log files',
        'options'     => [
            'days' => [
                'shortcut'    => 'd',
                'mode'        => InputOption::VALUE_OPTIONAL,
                'description' => 'Remove files older x days',
                'default'     => LoggerManager::DEFAULT_DAYS_LIMIT,
            ],
        ],
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $days = $input->getOption('days');
        if (is_numeric($days)) {
            Oforge()->Logger()->cleanupLogFiles((int) $days);
        } else {
            Oforge()->Logger()->cleanupLogFiles();
        }

        return self::SUCCESS;
    }
}
