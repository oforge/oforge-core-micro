<?php

namespace Oforge\Engine\Console\Commands\Cleanup\Cache;

use Oforge\Engine\Console\Abstracts\AbstractNamespaceCallCommand;

/**
 * Class NamespaceCallCommand
 *
 * @package Oforge\Engine\Console\Commands\Cleanup\Cache
 */
class NamespaceCallCommand extends AbstractNamespaceCallCommand {
    /** @var string $namespace */
    protected $namespace = 'cleanup:cache';

}
