<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractNamespaceCallCommand;

/**
 * Class ExampleNamespaceCallCommand
 *
 * @package Oforge\Engine\Console\Commands\Example
 */
class NamespaceCallCommand extends AbstractNamespaceCallCommand {
    /** @var string $namespace */
    protected $namespace = 'example:all'; // use namespace as defaultName if defaultName is not set.
    /** @var string[] $excludeCommands */
    protected $excludeCommands = ['example:batch'];

}
