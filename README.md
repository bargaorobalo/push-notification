Push Notification
=================

Permite o gerenciamento de dispositivo habilitados a receber notifições por push e o envio de notificações a partir de um servidor utilizando PHP. 

Pré-Requisitos:

- PostgreSQL
- php_pgsql.dll - deve está habilitado no PHP descomente a linha contendo `;extension=php_pgsql.dll` no php.ini.

### API

Todos os serviços a seguir retornam código **HttpStatus** para indicar sucesso ou erro ocorrido ao executar uma operação além da descrição do motivo do estado retornado via **X-Status-Reason**.



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
	
- Retorno (HttpStatus): 

	- 201 (Created): Se criar o dispositivo
	- 400 (Bad Request): Se a requisição for inválida
	- 409 (Conflict): Se já existir um dispositivo com o token informado
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
***Atualização do token de um dispositivo:***

- .../api.php/devices
- Método Http: Put
- Entrada: Json contendo os dados do dispositivo e o novo token: 	

Exemplo:
	
	{
		"old_token" : "token atual do dispositivo",
		"new_token": "novo token do dispositivo"
	}
	
- Retorno (HttpStatus): 

	- 204 (No Content): Se atualizar o dispositivo
	- 400 (Bad Request): Se a requisição for inválida
	- 404 (Not Found): Se não existir um dispositivo com o token informado
	- 500 (Internal Server Error): Se ocorrer erro no servidor	
	
***Remoção de dispositivo:***

- .../api.php/devices
- Método Http: Delete
- Entrada: Json contendo os dados do dispositivo a ser removido
	
Exemplo:
	
	{
		"token" : "token do dispositivo"
	}
	
- Retorno (HttpStatus): 

	- 204 (No Content): Se remover o dispositivo
	- 400 (Bad Request): Se a requisição for inválida
	- 404 (Not Found): Se não existir um dispositivo com o token informado
	- 500 (Internal Server Error): Se ocorrer erro no servidor

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
	
- Retorno (HttpStatus e JSON): 

	- 200 (No Content): Se a operação for efetuada com sucesso, incluirá um json com detalhes do resultado.
	- 400 (Bad Request): Se a requisição for inválida
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
Exemplo de json de sucesso completo:

	{
		"androidFailed" : false,
		"iosFailed" : false,
		"androidFailureReason" : null,
		"iosFailureReason" : null,
		"devicesNotNotified" : [0]
	}	
	
Exemplo de json de sucesso parcial (alguns dispositivo não receberam):

	{
		"androidFailed" : false,
		"iosFailed" : false,
		"androidFailureReason" : null,
		"iosFailureReason" : null,
		"devicesNotNotified" : [
			{
				"token" : "token1",
				"type" : 1,
				"user_id" : "11111111111"
			},
			{
				"token" : "token2",
				"type" : 2,
				"user_id" : "11111111111"
			}
		]
	}	
	
Exemplo de json com erro geral no envio ao IOS:

	{
		"androidFailed" : false,
		"iosFailed" : true,
		"androidFailureReason" : null,
		"iosFailureReason" : "stream_socket_client(): unable 		to connect to ssl://gateway.sandbox.push.apple.com:2195 (Connection refused)",
		"devicesNotNotified" : [0]
	}
	
Exemplo de json de sucesso parcial para o IOS e erro geral no envio ao android:

	{
		"androidFailed" : true,
		"iosFailed" : false,
		"androidFailureReason" : "MismatchSenderId",
		"iosFailureReason" : null,
		"devicesNotNotified" : [
			{
				"token" : "token3",
				"type" : 2,
				"user_id" : "11111111111"
			},
			{
				"token" : "token2",
				"type" : 2,
				"user_id" : "11111111111"
			}
		]
	}	