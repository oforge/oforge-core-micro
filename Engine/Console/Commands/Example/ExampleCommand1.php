<?php

namespace Oforge\Engine\Console\Commands\Example;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExampleCommand1
 *
 * @package Oforge\Engine\Console\Commands\Example
 */
class ExampleCommand1 extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'example:cmd1',
        'description' => 'Example command 1',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln($this->getDescription());

        return self::SUCCESS;
    }

}
