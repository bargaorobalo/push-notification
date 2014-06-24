<?php

namespace PushNotification\Authentication;

require_once __DIR__ . "/../Model/Application.php";
require_once __DIR__ . "/../Database/bootstrap.php";

/**
 * Gerencia aplicações
 *
 * Os tokens foram gerados utilizando: //base64_encode( openssl_random_pseudo_bytes(32));
 */
class ApplicationManager {

	const APPLICATION_REPOSITORY = "PushNotification\Model\Application";

	/**
	 * Busca os dispositivos cadastrados de um usuário
	 *
	 * @param string $token
	 *        	Token de acesso
	 * @return Application Aplicação
	 */
	public static function getApplicationByToken($token) {
		if (!$token || !is_string($token)) {
			return null;
		}

		global $entityManager;

		return $entityManager->getRepository(ApplicationManager::APPLICATION_REPOSITORY)
				->findBy(array("token" => $token));
	}
}