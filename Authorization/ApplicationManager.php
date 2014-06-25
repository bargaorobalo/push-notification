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
	 * Busca uma aplicação pelo seu identificador
	 *
	 * @param int $id
	 *        	Identificador
	 * @return \Application Aplicação
	 */
	public static function getApplication($id) {
		if (!$id || !is_int($id)) {
			return null;
		}

		global $entityManager;
		return $entityManager->find(ApplicationManager::APPLICATION_REPOSITORY, $id);
	}
}