<?php

namespace Oforge\Engine\Console\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConsoleHelper
 *
 * @package Oforge\Engine\Console\Helper
 */
class ConsoleHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * @param Command $command
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function printCommandHelp(Command $command, InputInterface $input, OutputInterface $output) : int {
        $help = new HelpCommand();
        $help->setCommand($command);

        return $help->run($input, $output);
    }

}
