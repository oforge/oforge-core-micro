<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExampleCommand2
 *
 * @package Oforge\Engine\Console\Commands\Example
 */
class Command2 extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'example:cmd2',
        'description' => 'Example command 2',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getDescription());

        return self::SUCCESS;
    }

}
