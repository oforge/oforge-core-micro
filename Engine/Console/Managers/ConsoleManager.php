<?php

namespace Oforge\Engine\Console\Managers;

use Exception;
use Oforge\Engine\Core\Exceptions\InvalidClassException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class ConsoleManager
 *
 * @package Oforge\Engine\Console\Managers
 */
class ConsoleManager {
    /** @var ConsoleManager $instance */
    private static $instance;
    /** @var array<string,int> $commandClasses */
    private static $commandClasses = [];
    /** @var Application $application */
    private $application;
    /** @var ConsoleOutput $output */
    private $output;
    /** @var ConsoleOutput $nullOutput */
    private $nullOutput;

    /**
     * ConsoleManager constructor.
     */
    public function __construct() {
        $application = new Application();
        $application->addCommands($this->collectCommands());
        $this->application = $application;
        $this->output      = new ConsoleOutput();
    }

    /**
     * @return ConsoleManager
     */
    public static function init() : ConsoleManager {
        if (!isset(self::$instance)) {
            self::$instance = new ConsoleManager();
        }

        return self::$instance;
    }

    /**
     * @param string[] $commandClasses
     *
     * @throws InvalidClassException
     */
    public static function registerCommandClasses($commandClasses) {
        foreach ($commandClasses as $commandClass) {
            if (!is_subclass_of($commandClass, Command::class)) {
                throw new InvalidClassException($commandClass, Command::class);
            }
            if (!isset(self::$commandClasses[$commandClass])) {
                self::$commandClasses[$commandClass] = 1;
            }
        }
    }

    /**
     * @throws Exception
     */
    public function run() {
        $output = $this->application->run(null, $this->output);
    }

    /**
     * @param string $name
     * @param string|array $args $args
     * @param bool $consoleOutput
     *
     * @return int
     * @throws Exception
     */
    public function callCommand(string $name, $args, bool $consoleOutput = true) {
        $command = $this->application->find($name);
        if (is_string($args)) {
            $args = new StringInput($args);
        } elseif (is_array($args)) {
            $args = new ArrayInput($args);
        }
        if (!isset($this->nullOutput)) {
            $this->nullOutput = new NullOutput();
        }
        $output = $consoleOutput ? $this->output : $this->nullOutput;

        return $command->run($args, $output);
    }

    /**
     * @return Command[]
     */
    protected function collectCommands() {
        $commands = [];
        foreach (self::$commandClasses as $commandClass => $any) {
            /** @var Command $command */
            $command = new $commandClass();

            $commands[$command->getName()] = $command;
        }
        ksort($commands);

        return $commands;
    }

}
