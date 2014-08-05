<?php

namespace PushNotification\Authorization;

require_once "ApplicationManager.php";

use Slim\Slim;
use PushNotification\Authentication\ApplicationManager;

/**
 * Autorização
 */
class Authorization {

	/**
	 * Verifica se uma aplicação está autorizada a utilizar a API
	 *
	 * @param string $accessToken
	 *        	Token de acesso a API
	 * @param string $data
	 *        	Dados recebidos
	 * @return boolean True se estiver autorizada, false caso contrário
	 */
	public static function isAuthorized($accessToken, $data) {
		global $log;

		if (!$accessToken || empty($accessToken)) {
			$log->Warn("AccessToken não informado.");
			return false;
		}

		try {
			// obtém o identificador da aplicação e a assinatura
			$tokenData = base64_decode($accessToken);

			if (!$tokenData) {
				return false;
			}

			$tokenObject = json_decode($tokenData);

			if (!$tokenObject) {
				return false;
			}

			// busca a aplicação pelo identificador
			$log->Debug("Buscando aplicação de id: ".$tokenObject->appId);
			$application = ApplicationManager::getApplication($tokenObject->appId);

			if (!$application) {
				$log->Warn("Aplicação não encontrada.");
				return false;
			}

			// objeto a ser assinado
			$toSign = $tokenObject->appId.$application->getSecret().$tokenObject->timestamp.$data;

			// recria a assinatura
			$signature = hash_hmac("sha256", $toSign, $application->getSecret(), true);

			// se as assinaturas baterem então está autorizado
			return base64_encode($signature) == $tokenObject->signature;
		} catch (\Exception $e) {
			$log->Error("Erro ao autenticar.".$e->getMessage());
			return false;
		}
	}
}