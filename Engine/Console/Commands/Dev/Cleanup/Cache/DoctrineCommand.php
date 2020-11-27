<?php

namespace Oforge\Engine\Console\Commands\Dev\Cleanup\Cache;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DoctrineCommand
 *
 * @package Oforge\Engine\Console\Commands\Dev\Cleanup\Cache
 */
class DoctrineCommand extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'dev:cleanup:cache:doctrine',
        'description' => 'Remove doctrine orm cache folder',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $directory = ROOT_PATH . Statics::DIR_CACHE_DB;

        if (FileSystemHelper::remove($directory, true)) {
            $output->writeln('Doctrine cache directory cleared.', OutputInterface::VERBOSITY_NORMAL);

            return self::SUCCESS;
        }
        $output->writeln('Doctrine cache directory could not be cleared.', OutputInterface::VERBOSITY_NORMAL);

        return self::FAILURE;
    }

}
