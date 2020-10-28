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
    public function __construct() {
        $this->setConfiguration('commands', [
            Commands\Core\PingCommand::class,
            Commands\Core\ProcessAsyncEventsCommand::class,
            Commands\Example\ExampleBatchCallCommand::class,
            Commands\Example\ExampleCommand1::class,
            Commands\Example\ExampleCommand2::class,
            Commands\Example\ExampleCommand3::class,
            Commands\Example\ExampleNamespaceCallCommand::class,
        ]);

        Oforge()->Events()->attach('Oforge:Extension:init', Event::SYNC, function(Event $event) {
            /** @var AbstractBootstrap $boostrap */
            $boostrap = $event->getDataValue('bootstrap');
            ConsoleManager::registerCommandClasses($boostrap->getConfiguration('commands'));
        });
    }

}
