Push Notification
=================

Permite o gerenciamento de dispositivo habilitados a receber notifições por push e o envio de notificações a partir de um servidor utilizando PHP. 

Pré-Requisitos:

- PostgreSQL
- php_pgsql.dll - deve está habilitado no PHP descomente a linha contendo `;extension=php_pgsql.dll` no php.ini.

### API

***Criação de Dispositivo:***

- .../api.php/devices
- Método Http: Post
- Entrada: Json contendo os dados do dispositivo: 

	- token: identificador de push
	- type: tipo do dispositivo (1 = Android, 2 = IOS)
	- user_id: identificador do usuário (CPF)

Exemplo:
	
	{
		"token" : "token do dispositivo",
		"type" : 1,
		"user_id" : "11111111111"
	}
	
***Atualização do token de um dispositivo:***

- .../api.php/devices
- Método Http: Put
- Entrada: Json contendo os dados do dispositivo e o novo token: 	

Exemplo:
	
	{
		"old_token" : "token atual do dispositivo",
		"new_token": "novo token do dispositivo"
	}
	
***Remoção de dispositivo:***

- .../api.php/devices
- Método Http: Delete
- Entrada: Json contendo os dados do dispositivo a ser removido

Exemplo:
	
	{
		"token" : "token do dispositivo"
	}	

***Envio de uma notificação:***

- .../api.php/notifications
- Método Http: Post
- Entrada: Json contendo os dados dos dispositivos que devem receber a notificação e os dados a serem enviados na notificação:
- Resultado: JSON contendo informações sobre se houve falha geral no envio de notificações ao android e/ou ios, motivo da falha geral ocorrida e lista de dispositivos que não receberam a notificação (falha parcial).

Exemplo de entrada:
	
	{
    	"devices" : 
    	[ 
    		{
    		    "token" : "token do dispositivo 1"
    		},
	    	{
    	    	"token" : "token do dispositivo 2"
    		},
	    	{
    	    	"token" :"token do dispositivo 3"
    		}
    	],
    	"message" : "mensagem",
	    "data" : {
        	 "some_data" : "exemplo",
        	 "badge" : 1
    	}
	}
	
Exemplo de resultado:

	{
		"androidFailed" : false
		"iosFailed" : true
		"androidFailureReason" : null
		"iosFailureReason": "stream_socket_client(): unable 		to connect to ssl://gateway.sandbox.push.apple.com:2195 (Connection refused)"
		"devicesNotNotified" : [0]
	}