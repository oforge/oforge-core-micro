<?php
require_once __DIR__ . '/_include.php';

use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

$blacksmith = BlackSmith::getInstance();
$blacksmith->forge(false);

/** @var EntityManager $entityManager */
$entityManager = Oforge()->DB()->getForgeEntityManager()->getEntityManager();
$helperSet     = ConsoleRunner::createHelperSet($entityManager);
$helperSet->set(new ConnectionHelper($entityManager->getConnection()), 'db');
$helperSet->set(new EntityManagerHelper($entityManager), 'em');
ConsoleRunner::run($helperSet, []);
