<?php

namespace Oforge\Engine\Console\Abstracts;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractBatchCallCommand
 *
 * @package Oforge\Engine\Console\Abstracts
 */
abstract class AbstractBatchCallCommand extends AbstractCommand {
    const OPTION_STOP_ON_ERROR = 'stop-on-error';
    /**
     * Array with other commands to be called with optional input.
     *      $commands = [
     *          'command_1', // Command name only, no input
     *          'command_1' => '<methodname>', // Command name with method name(for dynamic inout at runtime), must be in static class.
     *          'command_1' => 'arg_1 --option_1', // Command name with string input
     *          'command_1' => [ // Command name with array input
     *              // 'command' => 'command_1', // Command key, optional. If not present, key is prepended.
     *              'arg_1' => value,
     *              '--option_1' => value,
     *          ],
     *      ]
     *
     * @var array $commands
     */
    protected $commands = null;

    /**
     * AbstractBatchCallCommand constructor.
     *
     * @param string|null $name
     * @param array $commands
     * @throws RuntimeException
     */
    public function __construct(string $name = null, array $commands = []) {
        $this->commands = $this->commands ?? $commands;
        $this->checkBatchCallConfig();
        parent::__construct($name);
    }

    /** @inheritdoc */
    protected function configure() {
        $this->addOption(self::OPTION_STOP_ON_ERROR, null, InputOption::VALUE_NONE, 'Quit when a subcommand fails.');
        parent::configure();
    }

    /**
     * @inheritDoc
     * @throws RuntimeException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $this->checkBatchCallConfig();
        $stopOnError = $input->getOption(self::OPTION_STOP_ON_ERROR);
        foreach ($this->commands as $name => $args) {
            if (!is_string($name)) {
                $name = $args;
                $args = [];
            }
            if ($this->getName() === $name) {
                continue;
            }
            if (is_string($args) && method_exists($this, $args)) {
                $args = $this->$args();
            }
            $errorCode = $this->callOtherCommand($output, $name, $args);
            if ($errorCode === self::FAILURE && $stopOnError) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    /**
     * Validate command config
     *
     * @throws RuntimeException
     */
    private function checkBatchCallConfig() {
        if (!isset($this->commands)) {
            throw new RuntimeException("Property 'commands' not defined.");
        }
        if (empty($this->commands)) {
            throw new RuntimeException("Property 'commands' is empty.");
        }
        foreach ($this->commands as $name => $args) {
            if (!is_string($name)) {
                $name = $args;
                $args = [];
            }
            if (!is_string($name)) {
                throw new RuntimeException("Command name '$name' is not a string.");
            }
            if ($args === null) {
                throw new RuntimeException("Arguments of command '$name' is null.");
            }
            if (!(is_string($args) || is_array($args))) {
                throw new RuntimeException("Arguments of command '$name' is not a string or array.");
            }
        }
    }

}
