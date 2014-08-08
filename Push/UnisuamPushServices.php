<?php

namespace PushNotification\Push;

use PushNotification\Model\Device;

/**
 * Serviços de push da Unisuam
 */
class UnisuamPushServices {

	/**
	 * Cria um dispositivo
	 */
	public static function create($device) {
		global $log;

		try {
			$log->Info("Criando dispositivo na UNISUAM.");

			if ($device != null) {
				//TODO chamar serviço

				$log->Info("Dispositivo criado na UNISUAM com sucesso.");
			}
		} catch (\Exception $e) {
			$log->Warn($e);
			// o sistema deve continuar normalmente
		}
	}

	/**
	 * Remove um dispositivo
	 */
	public static function delete($device) {
		global $log;

		try {
			$log->Info("Removendo dispositivo da UNISUAM.");

			if ($device != null) {
				//TODO
				$log->Info("Dispositivo removido da UNISUAM com sucesso.");
			}
		} catch (\Exception $e) {
			$log->Warn($e);
			// o sistema deve continuar normalmente
		}
	}
}