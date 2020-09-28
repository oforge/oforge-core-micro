<?php

namespace Oforge\Engine\Console\Abstracts;

use Monolog\Logger;
use Oforge\Engine\Console\Lib\Input;
use Oforge\Engine\Console\Services\ConsoleService;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\StringHelper;

/**
 * Class AbstractBatchCommand.
 * A group command calls all (none-cronjob) child commands.
 *
 * @package Oforge\Engine\Console\Abstracts
 */
abstract class AbstractBatchCommand extends AbstractCommand {
    /**
     * @var ConsoleService $consoleService ;
     */
    private $consoleService;
    /**
     * @var array|string[] $excludeCommands
     */
    private $excludeCommands;

    /**
     * AbstractBatchCommand constructor.
     *
     * @param string $name
     * @param string[] $excludeCommands List of commands to exclude.
     * @param int $type
     *
     * @throws ServiceNotFoundException
     */
    public function __construct(string $name, $excludeCommands = [], int $type = self::TYPE_DEFAULT) {
        parent::__construct($name, $type);
        $this->excludeCommands = $excludeCommands;
        try {
            $this->consoleService = Oforge()->Services()->get('console');
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $prefix       = $this->getName() . AbstractCommand::SUBCOMMAND_SEPARATOR;
        $exclude      = array_flip($this->excludeCommands);
        $commandNames = [];
        $commands     = $this->consoleService->getCommands(self::ALL_TYPES);
        foreach ($commands as $command) {
            if (StringHelper::startsWith($command->getName(), $prefix) && !isset($exclude[$command->getName()])) {
                $commandNames[] = $command->getName();
            }
        }
        sort($commandNames);
        foreach ($commandNames as $command) {
            $output->notice('Run batch sub command: ' . $command);
            $this->consoleService->runCommand($command, '');
        }
    }

}
