<?php

namespace Oforge\Engine\Console\Commands\Cleanup\Cache;

use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Helper\FileSystemHelper;
use Oforge\Engine\Core\Helper\Statics;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BootstrapCommand
 *
 * @package Oforge\Engine\Console\Commands\Cleanup\Cache
 */
class BootstrapCommand extends AbstractCommand {
    /** @var string[] $config */
    protected $config = [
        'name'        => 'cleanup:cache:bootstrap',
        'description' => 'Remove bootstrap cache file',
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $directory = ROOT_PATH . Statics::DIR_CACHE . Statics::GLOBAL_SEPARATOR;

        $success = true;
        if (file_exists($directory . 'bootstrap.cache.php')) {
            // $success &= FileSystemHelper::remove($directory . 'bootstrap.cache');
            $success &= FileSystemHelper::remove($directory . 'bootstrap.cache.php');
        }
        //region Old caching files, TODO remove after core refactoring
        $success &= FileSystemHelper::remove($directory . 'Engine.cache');
        $success &= FileSystemHelper::remove($directory . 'Engine.cache.php');
        $success &= FileSystemHelper::remove($directory . 'Plugins.cache');
        $success &= FileSystemHelper::remove($directory . 'Plugins.cache.php');
        //endregion

        if ($success) {
            $output->writeln('Removed bootstrap cache file.', OutputInterface::VERBOSITY_NORMAL);

            return self::SUCCESS;
        }
        $output->writeln('Bootstrap cache file could not be cleared.', OutputInterface::VERBOSITY_NORMAL);

        return self::FAILURE;
    }

}
