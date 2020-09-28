<?php

namespace Oforge\Engine\Console\Commands\Core;

use Monolog\Logger;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Console\Lib\Input;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Services\PingService;

/**
 * Class PingCommand
 *
 * @package Oforge\Engine\Console\Commands\Core
 */
class PingCommand extends AbstractCommand {

    /**
     * PingCommand constructor.
     */
    public function __construct() {
        parent::__construct('ping', self::TYPE_DEFAULT);
        $this->setDescription('Ping Oforge');
    }

    /**
     * @inheritdoc
     * @throws ServiceNotFoundException
     */
    public function handle(Input $input, Logger $output) : void {
        try {
            /** @var PingService $pingService */
            $pingService = Oforge()->Services()->get('ping');
            $output->notice($pingService->me());
        } catch (ServiceNotFoundException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }
    }

}
