<?php

namespace Oforge\Engine\Console\Commands\Cleanup;

use GetOpt\GetOpt;
use GetOpt\Option;
use Monolog\Logger;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Console\Lib\Input;

/**
 * Class CleanupLogfilesCommand
 *
 * @package Oforge\Engine\Console\Commands\Cleanup
 */
class CleanupLogfilesCommand extends AbstractCommand {

    /**
     * CleanupLogfilesCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:cleanup:logs', self::TYPE_DEFAULT);
        $this->setDescription('Cleanup log files.');
        $this->addOptions([
            Option::create('d', 'days', GetOpt::OPTIONAL_ARGUMENT)#
                  ->setDescription('Remove files older x days')#
                  ->setValidation('is_numeric')->setDefaultValue(false),#
        ]);
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        if ($input->getOption('days')) {
            Oforge()->Logger()->cleanupLogfiles((int) $input->getOption('days'));
        } else {
            Oforge()->Logger()->cleanupLogfiles();
        }
    }

}
