<?php

namespace Oforge\Engine\Console\Commands\Dev;

use Oforge\Engine\Console\Abstracts\AbstractBatchCommand;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;

/**
 * Class DevCleanupBatchCommand
 * Run all oforge:dev:cleanup:* cleanup commands.
 *
 * @package Oforge\Engine\Console\Commands\Dev
 */
class DevCleanupBatchCommand extends AbstractBatchCommand {

    /**
     * ExampleBatchCommand constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct('oforge:dev:cleanup', [], self::TYPE_DEVELOPMENT);
        $this->setDescription('Run all oforge:dev:cleanup:* cleanup commands.');
    }

}
