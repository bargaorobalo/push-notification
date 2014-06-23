<?php

namespace PushNotification\Push;

require_once __DIR__ . "/../Database/bootstrap.php";

use PushNotification\Model\Device;

/**
 * Gerenciador de dispositivos
 */
class DeviceManager {

	const DEVICE_REPOSITORY = "PushNotification\Model\Device";

	/**
	 * Verifica se um dispositivo é válido
	 *
	 * @param Device $device
	 *        	Dispositivo
	 * @param string $validateDeviceType
	 *        	True se for para validar o tipo do dispositivo, false caso contrário
	 * @throws \InvalidArgumentException
	 */
	public static function validateDevice($device, $validateDeviceType = true) {
		// verifica se o identificador de push foi informado
		$token = $device->getToken();
		if (!$token || !is_string($token)) {
			throw new \InvalidArgumentException("O identificador do dispositivo não foi informado.");
		}

		// verifica se o usuário foi informado e se é um CPF
		$userId = $device->getUserId();
		if (!$userId || preg_match("/^[0-9]{11}$/", $userId) == 0) {
			throw new \InvalidArgumentException("O usuário não foi informado.");
		}

		// verifica o tipo foi informado e é uns dos tipos válidos
		$type = $device->getDeviceType();
		if ($validateDeviceType && (!$type || ($type !== Device::ANDROID && $type !== Device::IOS))) {
			throw new \InvalidArgumentException("O tipo do dispositivo não foi informado ou é inválido.");
		}
	}

	/**
	 * Busca todos os dispositivos
	 *
	 * @param int $page Página a ser retornada
	 * @param int $limit Limite de resultados a retornar
	 * @param string $order Campos para ordenação separados por vírgula
	 * @return Device[] Dispositivos
	 */
	public static function getDevices($page, $limit, $order) {

		// verifica se a página foi informada, se não for usa o padrão
		if (!$page) {
			$page = 1;
		} else if (!is_int($page)) {
			throw new \InvalidArgumentException("A página informada é inválida.");
		}

		//TODO ordenação

		// verifica se a ordenação foi informada, se não for usa o padrão
		if (!$order) {
			//$order  = "userId, type, token";
		} else if (!is_string($order)) {
			throw new \InvalidArgumentException("A ordenação informada é inválida.");
		}

		// verifica se o limite foi informado e é válido
		if (($limit && !is_int($limit))) {
			throw new \InvalidArgumentException("O limite informado é inválido.");
		}

		global $entityManager;

		$repository = $entityManager->getRepository(DeviceManager::DEVICE_REPOSITORY);

		if ($limit) {
			$offset = $limit * ($page - 1);
			return $repository->findBy(array(), null, $limit, $offset);
		} else {
			return $repository->findAll(array());
		}
	}

	/**
	 * Busca os dispositivos com os tokens informados
	 *
	 * @param string[] $userIds Identificadores dos usuários aos quais o dispositivos deve pertencer
	 * @return Device[] Dispositivos
	 */
	public static function getDevicesByUsers($userIds) {
		if (!$userIds || !is_array($userIds)) {
			throw new \InvalidArgumentException("O parãmetro informado é inválido.");
		}

		global $entityManager;

		return $entityManager->getRepository(DeviceManager::DEVICE_REPOSITORY)->findBy(array("userId" => $userIds));
	}

	/**
	 * Busca os dispositivos cadastrados de um usuário
	 *
	 * @param string $userId Identificador do usuário
	 * @return Devices[] Dispositivos
	 */
	public static function getDevicesByUserId($userId) {
		if (!$userId || !is_string($userId)) {
			throw new \InvalidArgumentException("O parãmetro informado é inválido.");
		}

		global $entityManager;

		return $entityManager->getRepository(DeviceManager::DEVICE_REPOSITORY)
								->findBy(array("userId" => $userId), array("type" => "ASC"));
	}

	/**
	 * Busca um dispositivo por seu identificador
	 *
	 * @param string $token Identificador do dispositivo
	 * @return Device Dispositivo
	 */
	public static function getDevice($token) {
		if (!$token || !is_string($token)) {
			return null;
		}

		global $entityManager;

		return $entityManager->getRepository(DeviceManager::DEVICE_REPOSITORY)->findOneBy(array("token" => $token));
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

		if (DeviceManager::exists($device->getToken())) {
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
	 * @param string $oldToken
	 *        	Identificador de push atual do dispositivo
	 * @param string $newToken
	 *        	Novo identificador de push do dispositivo
	 * @return boolean True se atualizou um dispositivo, false caso contrário
	 */
	public static function updateDeviceToken($oldToken, $newToken) {
		if (!$oldToken || !is_string($oldToken) || !$newToken || !is_string($newToken)) {
			return false;
		}

		global $entityManager;

		// busca o dispositivo
		$device = $entityManager->find(DeviceManager::DEVICE_REPOSITORY, array(
				"token" => $oldToken
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
	 * @param string $token
	 *        	Identificador de push do dispositivo
	 * @return boolean True se removeu um dispositivo, false caso contrário
	 */
	public static function deleteDevice($token) {
		if (!$token || !is_string($token)) {
			return false;
		}

		global $entityManager;

		$queryBuilder = $entityManager->createQueryBuilder()
							->delete(DeviceManager::DEVICE_REPOSITORY, "device")
							->where('device.token=:token')
							->setParameter('token', $token);

		$rows = $queryBuilder->getQuery()->execute();
		$entityManager->flush();

		return $rows > 0;
	}

	/**
	 * Verifica se um dispositivo existe
	 *
	 * @param string $token
	 *        	Identificador de push do dispositivo
	 */
	public static function exists($token) {
		if (!$token || !is_string($token)) {
			throw new \InvalidArgumentException("O identificador do dispositivo informado é inválido");
		}

		global $entityManager;

		$queryBuilder = $entityManager->createQueryBuilder();
		$queryBuilder->select('count(device.token)')
			->from(DeviceManager::DEVICE_REPOSITORY, 'device')
			->where('device.token=:token')
			->setParameter('token', $token);

		return $queryBuilder->getQuery()->getSingleScalarResult() > 0;
	}
}