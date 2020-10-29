<?php

namespace Oforge\Engine\Console\Abstracts;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCallCommand
 *
 * @package Oforge\Engine\Console\Abstracts
 */
abstract class AbstractCommand extends Command {
    /**
     * Configuration:
     *      $config = [
     *          'name'         => string, // Optional, alternative use $defaultName.
     *          'description'  => string, // Optional but recommended
     *          'aliases'      => string | string[], // optional
     *          'hidden'       => bool, // Optional, default=false
     *          'processTitle' => string, // Optional
     *          'usage'        => string | string[], // optional
     *          'help'         => string, // optional
     *          'arguments'    => [ // Optional
     *              '<name>' => [
     *                  'mode'        => InputArgument:: *, // Optional, default InputArgument::OPTIONAL,
     *                  'description' => string, // Optional but recommended
     *                  'default'     => mixed, // Optional
     *              ], // *
     *          ],
     *          'options'      => [ // Optional
     *              '<name>' => [ // Long option name ('--<name>').
     *                  'shortcut'    => string | string[], // Optional. The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     *                  'mode'        => InputOption::VALUE_ *, // Optional, default InputOption::VALUE_NONE,
     *                  'description' => string, // Optional but recommended
     *                  'default'     => mixed, // Optional. If mode=InputOption::VALUE_NONE, default will be ignored.
     *              ], // *
     *          ],
     *      ];
     *
     * @var array $config
     */
    protected $config = [];
    private $logger = null;

    /** @inheritdoc */
    protected function configure() {
        if (isset($this->config) && !empty($this->config)) {
            $config  = $this->config;
            $mapping = [
                'name'         => 'setName',
                'description'  => 'setDescription',
                'aliases'      => 'setAliases',
                'hidden'       => 'setHidden',
                'processTitle' => 'setProcessTitle',
                'usage'        => 'setProcessTitle',
                'help'         => 'setHelp',
            ];
            foreach ($mapping as $key => $method) {
                if (isset($config[$key])) {
                    $this->$method($config[$key]);
                }
            }
            if (isset($config['usage'])) {
                $usage = $config['usage'];
                if (!is_array($usage)) {
                    $usage = [$usage];
                }
                foreach ($usage as $u) {
                    $this->addUsage($u);
                }
            }
            if (isset($config['arguments']) && is_array($config['arguments'])) {
                foreach ($config['arguments'] as $argumentName => $argumentConfig) {
                    $mode        = $argumentConfig['mode'] ?? InputArgument::OPTIONAL;
                    $description = $argumentConfig['description'] ?? '';
                    $default     = $argumentConfig['default'] ?? null;
                    $this->addArgument($argumentName, $mode, $description, $default);
                }
            }
            if (isset($config['options']) && is_array($config['options'])) {
                foreach ($config['options'] as $optionName => $optionConfig) {
                    $shortcut    = $optionConfig['shortcut'] ?? null;
                    $mode        = $optionConfig['mode'] ?? InputOption::VALUE_NONE;
                    $description = $optionConfig['description'] ?? '';
                    $default     = $optionConfig['default'] ?? null;
                    if ($mode === InputOption::VALUE_NONE) {
                        $default = null;
                    }
                    $this->addOption($optionName, $shortcut, $mode, $description, $default);
                }
            }
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $name
     * @param string|array $args
     *
     * @return int
     * @throws Exception
     */
    protected function callOtherCommand(OutputInterface $output, string $name, $args) {
        $command = $this->getApplication()->find($name);
        if (is_string($args)) {
            $args = new StringInput($args);
        } elseif (is_array($args)) {
            $args = new ArrayInput($args);
        }

        return $command->run($args, $output);
    }

    // protected function getLogger(OutputInterface $output) { //not working yet, TODO howto combine of $output & logger
    //     if ($this->logger === null) {
    //         // $consoleLogger = new ConsoleLogger($output);
    //         //
    //         // $consoleLogger->
    //         //
    //         // $this->logger = $consoleLogger;
    //     }
    //
    //     return $this->logger;
    // }

}
