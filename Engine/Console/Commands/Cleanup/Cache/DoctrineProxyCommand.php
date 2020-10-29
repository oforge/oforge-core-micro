<?php

namespace Oforge\Engine\Console\Commands\Cleanup\Cache;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DoctrineProxyCommand
 *
 * @package Oforge\Engine\Console\Commands\Cleanup
 */
class DoctrineProxyCommand extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'cleanup:cache:proxy',
        'description' => 'Remove doctrine proxy cache folder',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $directory = ROOT_PATH . Statics::DIR_CACHE_PROXY;

        if (FileSystemHelper::remove($directory, true)) {
            $output->writeln('Doctrine proxy cache directory cleared.', OutputInterface::VERBOSITY_NORMAL);

            return self::SUCCESS;
        }
        $output->writeln('Doctrine proxy cache directory could not be cleared.', OutputInterface::VERBOSITY_NORMAL);

        return self::FAILURE;
    }

}
