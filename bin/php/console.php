<?php
require_once __DIR__ . '/_include.php';

$blackSmith = BlackSmith::getInstance();
$blackSmith->forge(false);

\Oforge\Engine\Console\Managers\ConsoleManager::init()->run();
