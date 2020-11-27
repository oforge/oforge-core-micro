<?php

namespace Oforge\Engine\Console;

use Oforge\Engine\Console\Managers\ConsoleManager;
use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Manager\Events\Event;

/**
 * Class Console-Bootstrap
 *
 * @package Oforge\Engine\Console
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Console-Bootstrap constructor.
     */
    protected function __construct() {
        parent::__construct();
        $this->setConfiguration('commands', [
            Commands\Oforge\PingCommand::class,
            Commands\Oforge\ProcessAsyncEventsCommand::class,
            Commands\Oforge\ServiceCommand::class,
            Commands\Cleanup\LogFilesCommand::class,
            Commands\Cleanup\Cache\BootstrapCommand::class,
            Commands\Cleanup\Cache\DoctrineProxyCommand::class,
            Commands\Cleanup\Cache\NamespaceCallCommand::class,
            Commands\Dev\Cleanup\Cache\DoctrineCommand::class,
            Commands\Example\BatchCallCommand::class,
            Commands\Example\Command1::class,
            Commands\Example\Command2::class,
            Commands\Example\Command3::class,
            Commands\Example\NamespaceCallCommand::class,
        ]);

        // TODO after core refactoring: attach only if extension active
        Oforge()->Events()->attach('Oforge:Extension:init', Event::SYNC, function (Event $event) {
            /** @var AbstractBootstrap $boostrap */
            $boostrap = $event->getDataValue('bootstrap');
            ConsoleManager::registerCommandClasses($boostrap->getConfiguration('commands'));
        });
    }

}
