<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractBatchCallCommand;

/**
 * Class ExampleBatchCallCommand
 *
 * @package Oforge\Engine\Console\Commands\Example
 */
class ExampleBatchCallCommand extends AbstractBatchCallCommand {
    /** @var string $defaultName */
    protected static $defaultName = 'example:batch';
    /** @var array $commands */
    protected $commands = ['example:cmd1', 'example:cmd3'];

}
