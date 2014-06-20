<?php
namespace PushNotification\Push;

require_once __DIR__."/../Database/bootstrap.php";

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
	 * @return Device Dispositivo criado
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
	 * @return Device
	 */
	public static function updateDeviceToken($device, $newToken) {
		// verifica se o dispositivo existe com o registrationId antigo e atualiza se existir
		if (DeviceManager::exists($device->getToken(), $device->getUserId())) {
			$device = $em->find('\Model\Device', array("token" => $device.getToken(), "userId" => device.getUserId()));
			$device->setToken($newToken);
			$entityManager->persist($device);
			$entityManager->flush();
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
		global $entityManager;

		$qb = $entityManager->createQueryBuilder();
		$qb->select('count(device.token)')
		->from('PushNotification\Model\Device','device')
		->where('device.token=:token')
		->andwhere('device.userId=:userId')
		->setParameter('token', $token)
		->setParameter('userId', $userId);
		return $qb->getQuery()->getSingleScalarResult() > 0;
	}
}