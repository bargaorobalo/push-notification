<?php
require_once __DIR__."/../vendor/autoload.php";
require_once __DIR__."/../Config/config.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

//string de conexão
$isDevMode = ENVIRONMENT == ENVIRONMENT_DEV;

//Configuração das entidades por anotação
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Model"), $isDevMode);

// obtendo o entity manager
// database configuration parameters
$dbParams = array(
    'driver'   => DB_DRIVER,
    'user'     => DB_USER,
    'password' => DB_PASSWORD,
    'dbname'   => DB_NAME,
    'host'     => DB_HOST,
    'port'     => DB_PORT
);

// obtaining the entity manager
$entityManager = EntityManager::create($dbParams, $config);