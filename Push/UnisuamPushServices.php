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
				UnisuamPushServices::callService(UNISUAM_CREATE_DEVICE_SERVICE, $device, $log);
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
				UnisuamPushServices::callService(UNISUAM_DELETE_DEVICE_SERVICE, $device, $log);
				$log->Info("Dispositivo removido da UNISUAM com sucesso.");
			}
		} catch (\Exception $e) {
			$log->Warn($e);
			// o sistema deve continuar normalmente
		}
	}

	/* gets the data from a URL */
	private static function callService($url, $device, $log)
	{
		$log->Debug("Chamando serviço da UNISUAM: ".$url);

		if(function_exists('curl_init')){
			$params = array();
			$params['token'] = $device->getToken();
			$params['userId'] = $device->getUserId();
			$params['type'] = $device->getDeviceType();

			$curl = curl_init();
			$timeout = 30;
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_URL, $url);

			//TODO: $data = curl_exec($curl);
			curl_close($curl);
			//$log->Debug($data);
		} else {
			throw new \Exception('curl lib não encontrada, por favor instale!');
		}
	}
}