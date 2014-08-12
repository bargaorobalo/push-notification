Push Notification
=================

Permite o gerenciamento de dispositivo habilitados a receber notifições por push e o envio de notificações a partir de um servidor utilizando PHP. 

### Pré-requisitos

O servidor apache deve estar configurado para repassar o cabeçalho de autorização para a aplicação.

A aplicação já contém um arquivo .htaccess que configura esse repasse, mas se desejado é possível incluir a mesma configuração no arquivo httpd.conf do apache e remover o arquivo .htaccess.

### Configuração

A configuração é feita através do arquivo config.php que está na pasta config, é possível configurar os seguintes dados:

- Ambiente
	- 	ENVIRONMENT: desenvolvimento (ENVIRONMENT_DEV) ou produção (ENVIRONMENT_PROD)
	
- Database:	
	- DB_DRIVER: driver a ser utilizado
	- DB_USER: usuário para acesso ao banco de dados
	- DB_PASSWORD: senha do usuário
	- DB_NAME: nome do banco de dados
	- DB_HOST: servidor do banco de dados
	- DB_PORT: porta do servidor do banco de dados
	
- Push
	- ANDROID_API_KEY: chave que permite o envio de push ao android, caso não tenha uma acesse o [manual](https://github.com/andrecrispim/push-notification/blob/master/docs/gcm_manual.md) sobre como criar uma chave de acesso para envio de notificações usando o *Google Cloud Messaging for Android*.
	- IOS_CERTIFICATE_PATH: caminho para o arquivo do certificado que permite o envio de push ao IOS
	- IOS_CERTIFICATE_PASSWORD: senha para o acesso ao certificado
	
- Autorização:
	- 	AUTHORIZATION_ENABLED: true se a autorização estiver habilitada, falso caso contrário (não deve ser desabilitada em produção).
	
- Acesso
	- CROSS_ORIGIN_ENABLED: Indica se permitirá o acesso entre domínios
	- ACCESS_CONTROL_ALLOW_ORIGIN: Domínios que terão acesso se o acesso entre domínios estiver habilitado
- Log (logentries.com)
	- LOG_LEVEL: Nível de log a ser enviado ao servidor do log entries, podendo ser um dos seguintes valores: 
		- LOG_EMERG, LOG_ALERT, LOG_CRIT, LOG_ERR, LOG_WARNING, LOG_NOTICE, LOG_INFO, LOG_DEBUG
	- LOG_SSL_ENABLED: indica se deve ou não usar uma conexão segura para envio dos logs
	- LOG_ENTRIES_TOKEN: token da aplicação no logentries


### API

Todos os serviços a seguir retornam código **HttpStatus** para indicar sucesso ou erro ocorrido ao executar uma operação além da descrição do motivo do estado retornado via **X-Status-Reason**.

Além disso, todos os serviços devem receber no cabeçalho HTTP o token de acesso a API.

***Criando um Token de Autorização***

Um aplicação cliente receberá acesso ao informar um token de acesso criado a partir dos dados de acesso disponibilizados:

	- appId: Identificador da aplicação
	- secret: Segredo da aplicação (esse segredo não deve ser tornar público).
	
Para gerar um token a aplicação deve concatena as seguintes informações (nessa ordem):
	
	- appId: Identificador da aplicação
	- secret: Segredo da aplicação
	- Unix timestamp : número de segundos desde 01/01/1970
	- data: dados a serem enviados via post, se existirem
		
A aplicação deve aplicar HmacSha256 sobre a string resultante utilizando como chave o segredo disponibilizado e retornando o resultado em base64.

Para finalizar a aplicação deve criar um json com os seguintes dados.

	- appId: Identificador da aplicação
	- timestamp: Unix timestamp utilizado no passo anterior
	- signature: resultado do HmacSha256
	
Exemplo:

	{
		"appId": 1,
		"timestamp": 1403701797,
		"signature": "TpRaYDhRf7r4IakcX8nQOTa+icPQvs0TFQVAfxiiUTA="
	};	

Esse json deve ser codificado em base64 e enviado no cabeçalho de autorização do HTTP (Authorization)	
	
	Authorization: Bearer eyJhcHBJZCI6MSwidGltZXN0YW1wIjoxNDAzNzAxNzk3LCJzaWduYXR1cmUiOiJUcFJhWURoUmY3cjRJYWtjWDhuUU9UYStpY1BRdnMwVEZRVkFmeGlpVVRBPSJ9
	
Exemplo em Javascript:

	var data = {
		users : [ {
			userId : "11111111111"
		},
		{
			userId : "22222222222"
		}],
		message : "Mensagem",
		data : {
         "badge" : 2,
         "custom" : [
           {
              "icon_url" : "http://..." 
           }
         ] 
		}
	}

	var secret = "appSecret";

	//dados a ser enviados
	var dataJson = JSON.stringify(data);

	var auth = {
		appId: 1,
	    timestamp: Math.floor((new Date).getTime() / 1000),
		signature: null
	};
	
	//dados a serem assinados
	var tokenData = auth.appId + secret + auth.timestamp + dataJson;

	//obtém a assinatura
	auth.signature = CryptoJS.HmacSHA256(tokenData, secret).toString(CryptoJS.enc.Base64);

	var token = JSON.stringify(obj);
	var tokenUtf8 = CryptoJS.enc.Utf8.parse(token);
	var tokenBase64 = CryptoJS.enc.Base64.stringify(tokenUtf8);

	$.ajax({
		url: "http://localhost/api.php/notifications",
		type: 'POST',
		beforeSend: function (xhr) {
			xhr.setRequestHeader('Authorization', 'Bearer ' + tokenBase64);
		},
		data: dataJson,
		contentType: 'application/json',
		success: successCallback,
		error: errorCallback
	});
	
Exemplo em **PHP**:

	...
	$secret = "appSecret";
	
	//dados a ser enviados
	$dataJson = json_encode($data);
	
	$auth = array('appId' => 1, 'timestamp' => time(), 'signature' => null);

	// dados a serem assinados
	$tokenData = $auth['appId'].$secret.$auth['timestamp'].$dataJson;

	// obtém a assinatura
	$auth['signature'] = base64_encode(hash_hmac("sha256", $tokenData, $secret, true));
	$token = json_encode($auth);

	//token
	$tokenBase64 = base64_encode($token);
	...

***Consulta de Usuários que possuem dispositivos cadastrados***

- .../api.php/users?page=1&limit=2
- Método Http: Get
- Parâmetros (opcionais)

	- page : página a ser retornada
	- limit: quantidade máxima de resultados a retornar
	 
- Retorno (HttpStatus e Json contendo os identificadores dos usuários, a página atual e o total de páginas): 

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
		"page" : 1,
		"totalPages" : 3
	} 

***Consulta de dispositivos cadastrados***

- .../api.php/devices?page=1&limit=2
- Método Http: Get
- Parâmetros (opcionais)

	- page : página a ser retornada
	- limit: quantidade máxima de resultados a retornar
	 
- Retorno (HttpStatus e Json contendo os dispositivos, a página atual e o total de páginas): 

	- 200 (OK): Se consultar com sucesso
	- 400 (Bad Request): Se a requisição for inválida
	- 401 (Unauthorized): Se o acesso for negado
	- 500 (Internal Server Error): Se ocorrer erro no servidor
	
Exemplo:

	{
	   "devices" : [
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
		],
		"page" : 1,
		"totalPages" : 5
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
           "badge" : 2,
           "custom" : [
              {
                 "icon_url" : "http://..." 
              }
           ] 
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