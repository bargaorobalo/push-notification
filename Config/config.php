<?php
// modo de execução (algumas apis utilizadas necessitam sabe o ambiente sendo utilizado
define( 'ENVIRONMENT_DEV', 	'dev');
define( 'ENVIRONMENT_PROD', 'prod');
define( 'ENVIRONMENT', 		ENVIRONMENT_PROD);

// database configuration
define( 'DB_DRIVER',   	'pdo_pgsql' );
define( 'DB_USER',     	'postgres' );
define( 'DB_PASSWORD', 	'adm123' );
define( 'DB_NAME',		'Unisuam');
define( 'DB_HOST',     	'localhost' );
define( 'DB_PORT',  	'5432' );

// push - Android
define( 'ANDROID_API_KEY', 			"AIzaSyABnEeaeeQXmAkUm795SFN16Z6_hrTFbho");

// push - IOS
define( 'IOS_CERTIFICATE_PATH', 	'unisuam/UnisuamAlunosCK.pem');
define( 'IOS_CERTIFICATE_PASSWORD', 'unisuam-alunos');

// Autorização
define( 'AUTHORIZATION_ENABLED', false);

// Acesso
define( 'CROSS_ORIGIN_ENABLED', true);
define( 'ACCESS_CONTROL_ALLOW_ORIGIN', "*");