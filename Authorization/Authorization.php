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
	 * @param string $token
	 *        	Token de acesso a API
	 * @return boolean True se estiver autorizada, false caso contrário
	 */
	public static function isAuthorized($token) {
		if (!$token || empty($token)) {
			return false;
		}

		return ApplicationManager::getApplicationByToken($token) != null;
	}
}