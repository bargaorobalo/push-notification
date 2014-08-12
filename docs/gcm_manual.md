#GCM - Criação de conta

Para poder enviar notificações para dispositivos Android é necessário criar um projeto e uma chave de acesso a este projeto. Para isso acesse o [Google Developer Console](https://console.developers.google.com).

* Primeiro é necessário criar um projeto
	* acesse a [lista de projetos](https://console.developers.google.com/project?authuser=0)
	* Clique em *Create Project*
	* Na janela que abriu informe o nome do projeto
	* Informe um identificador único para o projeto ou aceite um gerado pelo google
	* Aceite o termos do serviço e clique em Create
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-1.png)
	
	* A página do projeto será aberta automaticamente
	
* Habilite a api do *Google Cloud Messaging for Android*
	* Acesse as APIs em *APIS & AUTH -> APIS*
	* Procure o item *Google Cloud Messaging for Android* e clique no botão *OFF* para habilitá-la.
	* Ela deve ir para a parte superior da tela com um botão *ON* indicando que está ativa.
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-2.png)
	
* Gere uma chave de acesso
	* Acesse as credenciais em *APIS & AUTH -> Credentials*
	* Clique no botão *Create New Key* na sessão *Public API Access*
	* Na janela que abriu clique no botão "Server Key"
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-5.png)
	
	* Na janela seguinte irá se exibido um campo para informar os IPs dos servidores que podem enviar requisições utilizando a chave sendo criada. Se desejar é possível deixar o campo em banco, o que significará que qualquer servidor poderá utilizar a chave para enviar requisições.
	* Clique em *Create*
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-3.png)
	
	* Irá ser exibida uma nova sessão chamada *Key for server applications*, copie a chave dessa sessão (*API KEY*) e cole-a no arquivo de configuração na chave *ANDROID_API_KEY*
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-4.png)
	
* Configure a aplicacão Android
	* A aplicação android necessitará do número do projeto para ser registrar no servidores do google
	* Obtenha o número de acesso na opção *Monitoring -> Overview*, ele se encontra na parte superior da tela ao lado do *Project Id* com o nome *Project Number*
	
	![Image](https://raw.githubusercontent.com/andrecrispim/push-notification/master/docs/resources/push-step-6.png)
	
	* Na aplicação da UNISUAM ele deve ser configurado no módulo de configuração *configModule.js*, procure por SENDER_ID e substitua o número pelo número do seu projeto.
	
	