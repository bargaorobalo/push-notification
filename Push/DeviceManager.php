<?php

namespace PushNotification\Push;

require_once __DIR__ . "/../Database/bootstrap.php";

use PushNotification\Model\Device;

/**
 * Gerenciador de dispositivos
 */
class DeviceManager {

	/**
	 * Insere um dispositivo
	 *
	 * @param Device $device
	 *        	Dados do dispositivo
	 * @return Device Dispositivo criado ou null se um dispositivo
	 *         com o mesmo token e usuário já existir
	 */
	public static function insertDevice($device) {
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