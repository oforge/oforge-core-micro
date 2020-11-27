<?php

namespace Oforge\Engine\Console\Commands\Oforge;

use Exception;
use Oforge\Engine\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Core\Helper\StringHelper;
use ReflectionException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ServiceCommand
 *
 * @package Oforge\Engine\Console\Commands\Oforge
 */
class ServiceCommand extends AbstractCommand {
    /** @var array $config */
    protected $config = [
        'name'        => 'oforge:service',
        'description' => 'Caller for oforge service methods',
        'hidden'      => true,
        'arguments'   => [
            'ServiceMethod'         => [
                'description' => 'Callable of service method (Format: <ServiceName>:<ServiceMethod>)',
                'default'     => '',
            ],
            'ServiceMethodArgument' => [
                'mode'        => InputArgument::OPTIONAL | InputArgument::IS_ARRAY,
                'description' => 'Argument(s) for service method',
                'default'     => [],
            ],
        ],
    ];

    /** @inheritdoc */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $serviceMethod = $input->getArgument('ServiceMethod');
        if (empty($serviceMethod)) {
            $this->renderList($output, '');
        } else {
            $serviceArgs = $input->getArgument('ServiceMethodArgument');
            $this->callServiceMethod($output, $serviceMethod, $serviceArgs);
        }

        return self::SUCCESS;
    }

    /**
     * @param OutputInterface $output
     * @param string $call
     * @param array $serviceArgs
     */
    private function callServiceMethod(OutputInterface $output, string $call, array $serviceArgs) {
        $parts         = explode(':', $call);
        $serviceName   = $parts[0] ?? '';
        $serviceMethod = $parts[1] ?? null;
        try {
            $service = Oforge()->Services()->get($serviceName);
            if ($serviceMethod === null || !method_exists($service, $serviceMethod)) {
                $this->renderList($output, $serviceName);
            } else {
                $callable = [$service, $serviceMethod];
                $return   = call_user_func_array($callable, $serviceArgs);
                if (!empty($return) || is_numeric($return)) {
                    $output->writeln(is_scalar($return) ? $return : print_r($return, true));
                }
            }

        } catch (Exception $exception) {
            $this->renderList($output, '');
        }
    }

    /**
     * @param OutputInterface $output
     * @param string $prefix
     */
    private function renderList(OutputInterface $output, string $prefix) {
        $serviceManager = Oforge()->Services();
        $serviceNames   = $serviceManager->getServiceNames();
        sort($serviceNames);
        $table = new Table($output);
        $table->setHeaders(['Service method', 'Description']);
        foreach ($serviceNames as $index => $serviceName) {
            if ($index > 0 && $prefix === '') {
                $table->addRow(new TableSeparator());
            }
            try {
                $service        = $serviceManager->get($serviceName);
                $serviceMethods = get_class_methods($service);
                foreach ($serviceMethods as $serviceMethod) {
                    if (StringHelper::startsWith($serviceMethod, '__')) {
                        continue;
                    }
                    $call = $serviceName . ':' . $serviceMethod;
                    if (!($prefix === '' || StringHelper::startsWith($call, $prefix))) {
                        continue;
                    }
                    $row = [$call];
                    $doc = '-';
                    try {
                        $reflector    = new \ReflectionClass($service);
                        $commentLines = explode("\n", $reflector->getMethod($serviceMethod)->getDocComment());
                        if (count($commentLines) > 1) {
                            if (!StringHelper::contains($commentLines[1], '@param')
                                && !StringHelper::contains($commentLines[1], '@throws')
                                && !StringHelper::contains($commentLines[1], '@return')) {
                                $doc = strtr($commentLines[1], [
                                    '*'  => '',
                                    "\n" => '',
                                ]);
                            }
                        }
                    } catch (ReflectionException $exception) {
                    }
                    $row[] = trim($doc);

                    $table->addRow($row);
                }
            } catch (ServiceNotFoundException $exception) {
            }
        }
        $table->render();
    }

}
