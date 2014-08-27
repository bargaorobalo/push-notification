<?php
// modo de execução (algumas apis utilizadas necessitam saber o ambiente sendo utilizado
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
define( 'ANDROID_API_KEY', "AIzaSyABnEeaeeQXmAkUm795SFN16Z6_hrTFbho");

// push - IOS
define( 'IOS_CERTIFICATE_PATH', 	'unisuam/UnisuamAlunosCK.pem');
define( 'IOS_CERTIFICATE_PASSWORD', 'unisuam-alunos');

// Autorização
define( 'AUTHORIZATION_ENABLED', true);
define( 'AUTHORIZATION_HEADER', 'UnisuamAuth');

// Acesso
define( 'CROSS_ORIGIN_ENABLED', true);
define( 'ACCESS_CONTROL_ALLOW_ORIGIN', "*");

// Log
define( 'LOG_LEVEL', LOG_DEBUG); //pode ser LOG_EMERG, LOG_ALERT, LOG_CRIT, LOG_ERR, LOG_WARNING, LOG_NOTICE, LOG_INFO, LOG_DEBUG
define( 'LOG_SSL_ENABLED', true);
define( 'LOG_ENTRIES_TOKEN', '0774c510-01d4-48f5-8fe0-12bedde18bbe');

//UNISUAM
define( 'UNISUAM_CREATE_DEVICE_SERVICE', "http://apicms-dev.unisuam.edu.br/api/cms-push");
define( 'UNISUAM_DELETE_DEVICE_SERVICE', "http://apicms-dev.unisuam.edu.br/api/cms-push/desativar");
define( 'UNISUAM_APP_TOKEN', "");