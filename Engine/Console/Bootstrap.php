<?php

namespace Oforge\Engine\Console;

use Oforge\Engine\Console\Commands\Cleanup\CleanupLogfilesCommand;
use Oforge\Engine\Console\Commands\Console\CommandListCommand;
use Oforge\Engine\Console\Commands\Core\PingCommand;
use Oforge\Engine\Console\Commands\Core\ProcessAsyncEventsCommand;
use Oforge\Engine\Console\Commands\Dev\DevCleanupBatchCommand;
use Oforge\Engine\Console\Commands\Dev\DevDoctrineOrmCacheCleanupCommand;
use Oforge\Engine\Console\Commands\Doctrine\DoctrineOrmWrapperCommand;
use Oforge\Engine\Console\Commands\Example\ExampleBatchCommand;
use Oforge\Engine\Console\Commands\Example\ExampleCommandOne;
use Oforge\Engine\Console\Commands\Example\ExampleCommandThree;
use Oforge\Engine\Console\Commands\Example\ExampleCommandTwo;
use Oforge\Engine\Console\Commands\Example\ExampleGroupCommand;
use Oforge\Engine\Console\Commands\Service\ServiceListCommand;
use Oforge\Engine\Console\Commands\Service\ServiceRunCommand;
use Oforge\Engine\Console\Services\ConsoleService;
use Oforge\Engine\Core\Abstracts\AbstractBootstrap;

/**
 * Class Console-Bootstrap
 *
 * @package Oforge\Engine\Console
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Console-Bootstrap constructor.
     */
    public function __construct() {
        $this->commands = [
            CleanupLogfilesCommand::class,
            DevCleanupBatchCommand::class,
            DevDoctrineOrmCacheCleanupCommand::class,
            CommandListCommand::class,
            DoctrineOrmWrapperCommand::class,
            ExampleBatchCommand::class,
            ExampleGroupCommand::class,
            ExampleCommandOne::class,
            ExampleCommandTwo::class,
            ExampleCommandThree::class,
            PingCommand::class,
            ProcessAsyncEventsCommand::class,
            ServiceListCommand::class,
            ServiceRunCommand::class,
        ];
        $this->services = [
            'console' => ConsoleService::class,
        ];
    }

}
