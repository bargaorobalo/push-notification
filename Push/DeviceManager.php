<?php

namespace Unisuam\Push;

use Unisuam\Model\Device;

/**
 * Gerenciador de dispositivos
 */
class DeviceManager {

	/**
	 * Insere um dispositivo
	 *
	 * @param Device $device
	 *        	Dados do dispositivo
	 * @return Device Dispositivo criado
	 */
	public static function insertDevice($device) {
		if (DeviceManager::exists($device->getToken(), $device->getUserId())) {
			return $device;
		}

		// TODO criar

		return $device;
	}

	/**
	 * Atualiza o identificador de push de um dispositivo
	 *
	 * @param Device $device
	 *        	Dispositivo
	 * @param string $newToken
	 *        	Novo identificador de push do dispositivo
	 * @return Device
	 */
	public static function updateDeviceRegistrationId($device, $newToken) {
		// verifica se o dispositivo existe com o registrationId antigo e atualiza se existir
		if (DeviceManager::exists($device->getToken(), $device->getUserId())) {
			// TODO atualizar
			return $device;
		}
	}

	/**
	 * Remove um dispositivo
	 *
	 * @param Device $device
	 *        	Dispositivo
	 */
	public static function deleteDevice($device) {
		// TODO remover dispositivo
	}

	/**
	 * Verifica se um dispositivo existe
	 *
	 * @param string $token
	 *        	Identificador de push do dispositivo
	 * @param string $userId
	 *        	Usuário ao qual o dispositivo está associado
	 */
	public static function exists($token, $userId) {
		// TODO verificar se existe um dispositivo do usuário com o token informado
	}
}