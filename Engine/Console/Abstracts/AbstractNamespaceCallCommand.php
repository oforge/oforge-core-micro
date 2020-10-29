<?php

namespace Oforge\Engine\Console\Abstracts;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractNamespaceCallCommand
 *
 * @package Oforge\Engine\Console\Abstracts
 */
abstract class AbstractNamespaceCallCommand extends AbstractCommand {
    const OPTION_STOP_ON_ERROR = 'stop-on-error';
    /**
     * Namespace of commands to be called, eg oforge:clear.
     * If defaultName is not set, namespace is uses as defaultName.
     *
     * @var string $namespace
     */
    protected $namespace = null;
    /**
     * Command names to be excluded in batch run.
     *
     * @var string[] $excludeCommands
     */
    protected $excludeCommands = [];

    /**
     * AbstractNamespaceCallCommand constructor.
     *
     * @param string|null $namespace
     * @param array $excludeCommands
     */
    public function __construct(string $namespace = null, array $excludeCommands = []) {
        $this->namespace       = rtrim($this->namespace ?? trim($namespace), ':');
        $this->excludeCommands = $this->excludeCommands ?? (empty($excludeCommands) ? null : $excludeCommands);
        $this->checNamespaceCallConfig();
        parent::__construct(static::$defaultName ?? $this->namespace);
    }

    /** @inheritdoc */
    protected function configure() {
        $this->addOption(self::OPTION_STOP_ON_ERROR, null, InputOption::VALUE_NONE, 'Quit when a subcommand fails.');
        $this->setDescription("Call all commands of namespace: '{$this->namespace}'");
        parent::configure();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $this->checNamespaceCallConfig();
        $stopOnError = $input->getOption(self::OPTION_STOP_ON_ERROR);
        $commands    = $this->getApplication()->all($this->namespace);
        if (!is_array($commands)) {
            $commands = [$commands];
        }
        $exclude = array_flip($this->excludeCommands);
        foreach ($commands as $command) {
            if ($command->getName() === $this->getName() || isset($exclude[$command->getName()])) {
                continue;
            }
            $errorCode = $this->callOtherCommand($output, $command->getName(), []);
            if ($errorCode === self::FAILURE && $stopOnError) {
                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    private function checNamespaceCallConfig() {
        if (empty($this->namespace)) {
            throw new RuntimeException("Property 'namespace' not defined.");
        }
    }
}
