<?php

namespace PushNotification\Push;

require_once __DIR__ . "/../Database/bootstrap.php";

use PushNotification\Model\Device;

/**
 * Gerenciador de dispositivos
 */
class DeviceManager {

	/**
	 * Verifica se um dispositivo é válido
	 *
	 * @param Device $device Dispositivo
	 * @param string $validateDeviceType True se for para validar o tipo do dispositivo, false caso contrário
	 * @throws \InvalidArgumentException
	 */
	public static function validateDevice($device, $validateDeviceType = true) {
		// verifica se o identificador de push foi informado
		if (!$device->getToken()) {
			throw new \InvalidArgumentException("O identificador do dispositivo não foi informado.");
		}

		// verifica se o usuário foi informado e se é um CPF
		$userId = $device->getUserId();
		if (!$userId || preg_match("/^[0-9]{11}$/", $userId) == 0) {
			throw new \InvalidArgumentException("O usuário não foi informado.");
		}

		// verifica o tipo foi informado e é uns dos tipos válidos
		$type = $device->getDeviceType();
		if ($validateDeviceType && (!$type || ($type != Device::ANDROID && $type != Device::IOS))) {
			throw new \InvalidArgumentException("O tipo do dispositivo não foi informado ou é inválido.");
		}
	}

	/**
	 * Insere um dispositivo
	 *
	 * @param Device $device
	 *        	Dados do dispositivo
	 * @return Device Dispositivo criado ou null se um dispositivo
	 *         com o mesmo token e usuário já existir
	 */
	public static function insertDevice($device) {
		DeviceManager::validateDevice($device);

		if (DeviceManager::exists($device->getToken(), $device->getUserId())) {
			return null;
		}

		global $entityManager;
		$entityManager->persist($device);
		$entityManager->flush();

		return $device;
	}

	/**
	 * Atualiza o identificador de push de um dispositivo
	 *
	 * @param Device $device
	 *        	Dispositivo
	 * @param string $newToken
	 *        	Novo identificador de push do dispositivo
	 * @return boolean True se atualizou um dispositivo, false caso contrário
	 */
	public static function updateDeviceToken($device, $newToken) {
		DeviceManager::validateDevice($device, false);

		global $entityManager;

		// busca o dispositivo
		$device = $entityManager->find('PushNotification\Model\Device', array(
				"token" => $device->getToken(),
				"userId" => $device->getUserId()
		));

		// se existir atualiza-o
		if ($device) {
			$device->setToken($newToken);
			$entityManager->persist($device);
			$entityManager->flush();
			return true;
		}

		return false;
	}

	/**
	 * Remove um dispositivo
	 *
	 * @param Device $device
	 *        	Dispositivo
	 * @return boolean True se removeu um dispositivo, false caso contrário
	 */
	public static function deleteDevice($device) {
		DeviceManager::validateDevice($device, false);

		global $entityManager;

		$query = $entityManager->createQueryBuilder()
					->delete('PushNotification\Model\Device', "device")
					->where('device.token=:token')
					->andwhere('device.userId=:userId')
					->setParameter('token', $device->getToken())
					->setParameter('userId', $device->getUserId());

		$rows = $query->getQuery()->execute();
		$entityManager->flush();

		return $rows > 0;
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
		global $entityManager;

		$qb = $entityManager->createQueryBuilder();
		$qb->select('count(device.token)')
				->from('PushNotification\Model\Device', 'device')
				->where('device.token=:token')
				->andwhere('device.userId=:userId')
				->setParameter('token', $token)
				->setParameter('userId', $userId);
		return $qb->getQuery()->getSingleScalarResult() > 0;
	}
}