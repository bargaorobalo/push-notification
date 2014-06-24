Push Notification
=================

Permite o gerenciamento de dispositivo habilitados a receber notifições por push e o envio de notificações a partir de um servidor utilizando PHP. 

Pré-Requisitos:

- PostgreSQL
- php_pgsql.dll - deve está habilitado no PHP descomente a linha contendo `;extension=php_pgsql.dll` no php.ini.

### API

Todos os serviços a seguir retornam código **HttpStatus** para indicar sucesso ou erro ocorrido ao executar uma operação além da descrição do motivo do estado retornado via **X-Status-Reason**.

Além disso, todos os serviços devem receber no cabeçalho HTTP o token de acesso a API (`Authorization token`).

***Consulta de Usuários que possuem dispositivos cadastrados***

- .../api.php/users?page=1&limit=2
- Método Http: Get
- Parâmetros (opcionais)

	- page : página a ser retornada
	- limit: quantidade máxima de resultados a retornar
	 
- Retorno (HttpStatus e Json contendo os identificadores dos usuários e quantidade total de usuários encontrados): 

	- 200 (OK): Se consultar com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
Exemplo:

	{
		"users" : [
			{
				"userId" : "11111111111"
			},	
			{
				"userId" : "22222222222"
			}
		],
		"total" : 5		
	} 

***Consulta de Dispositivos de um Usuário:***

- .../api.php/users/{:userId}/devices
- Método Http: Get
- Retorno (HttpStatus e Json contendo os dados do dispositivos): 

	- 200 (OK): Se consultar com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
Exemplo:

	[
		{		
			"token" : "token2",
			"type" : 1,
			"userId" : "11111111111"
		},
		{
			"token" : "token3",
			"type" : 2,
			"userId" : "11111111111"
		}
	]

***Criação de Dispositivo:***

- .../api.php/devices
- Método Http: Post
- Entrada: Json contendo os dados do dispositivo: 

	- token: identificador de push
	- type: tipo do dispositivo (1 = Android, 2 = IOS)
	- userId: identificador do usuário (CPF)

Exemplo:
	
	{
		"token" : "token do dispositivo",
		"type" : 1,
		"userId" : "11111111111"
	}
	
- Retorno (HttpStatus): 

	- 201 (Created): Se criar o dispositivo com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
	- 409 (Conflict): Se já existir um dispositivo com o token informado
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
***Atualização do token de um dispositivo:***

- .../api.php/devices
- Método Http: Put
- Entrada: Json contendo os dados do dispositivo e o novo token: 
	
Exemplo: 
	
	{
		"oldToken" : "token atual do dispositivo",
		"newToken": "novo token do dispositivo",
		"userId" : "11111111111"
	}
	
- Retorno (HttpStatus): 

	- 204 (No Content): Se atualizar o dispositivo com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
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

	- 204 (No Content): Se remover o dispositivo com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
	- 404 (Not Found): Se não existir um dispositivo com o token informado
	- 500 (Internal Server Error): Se ocorrer erro no servidor

***Envio de uma notificação:***

- .../api.php/notifications
- Método Http: Post
- Entrada: Json contendo os dados dos dispositivos que devem receber a notificação e os dados a serem enviados na notificação:

Exemplo de entrada:
	
	{
    	"users" : 
    	[ 
    		{
    		    "userId" : "identificador usuário 1"
    		},
	    	{
    	    	"userId" : "identificador usuário 2"
    		},
	    	{
    	    	"userId" : "identificador usuário 3"
    		}
    	],
    	"message" : "mensagem",
	    "data" : {
        	 "someData" : "exemplo",
        	 "badge" : 1
    	}
	}
	
- Retorno (HttpStatus e JSON contendo informações sobre se houve falha geral no envio de notificações ao android e/ou ios, motivo da falha geral ocorrida e lista de dispositivos que não receberam a notificação (falha parcial): 

	- 200 (OK): Se a operação for efetuada com sucesso, incluirá um json com detalhes do resultado.
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
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
				"userId" : "11111111111"
			},
			{
				"token" : "token2",
				"type" : 2,
				"userId" : "11111111111"
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
				"userId" : "11111111111"
			},
			{
				"token" : "token2",
				"type" : 2,
				"userId" : "11111111111"
			}
		]
	}	