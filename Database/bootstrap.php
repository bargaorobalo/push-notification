<?php
require_once  __DIR__."/../vendor/autoload.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

//string de conexão
$dsn = "pgsql:dbname=Unisuam;host=localhost;user=postgres;password=adm123";
$isDevMode = true;

//Configuração das entidades por anotação
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Model"), $isDevMode);

// obtendo o entity manager
// database configuration parameters
$dbParams = array(
    'driver'   => 'pdo_pgsql',
    'user'     => 'postgres',
    'password' => 'adm123',
    'dbname'   => 'Unisuam',
    'host'     => 'localhost',
    'port'     => '5432',
);

// obtaining the entity manager
$entityManager = EntityManager::create($dbParams, $config);