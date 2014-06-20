<?php

namespace PushNotification\Model;

/**
 * Dispositivo
 */
class Device implements \JsonSerializable {
	/**
	 * Dispositivo android
	 */
	const ANDROID = 1;

	/**
	 * Dispositivo IOS
	 */
	const IOS = 2;
	protected $token;
	protected $type;
	protected $userId;

	/**
	 * Tipo do dispositivo
	 *
	 * @return int
	 */
	public function getDeviceType() {
		return $this->type;
	}

	/**
	 * Identificador do dispositivo para recebimento de notificações
	 *
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Identificador do usuário
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * Constructor
	 *
	 * @param string $token
	 *        	Identificador do dispositivo para recebimento de notificações
	 * @param int $type
	 *        	Tipo do dispositivo
	 * @param string $userId
	 *        	Identificador do usuário
	 */
	public function __construct($token, $type, $userId) {
		if (!$token) {
			throw new \InvalidArgumentException("O identificador do dispositivo é obrigatório.");
		}

		if (!$type) {
			throw new \InvalidArgumentException("O tipo do dispositivo é obrigatório.");
		}

		// verifica se o usuário foi informado e se é um CPF
		if (!$userId || preg_match("/^[0-9]{11}$/", $userId) == 0) {
			throw new \InvalidArgumentException("O usuário é obrigatório.");
		}

		$this->token = $token;

		if ($type === Device::ANDROID || $type === Device::IOS) {
			$this->type = $type;
		} else {
			throw new \InvalidArgumentException("O tipo de dispositivo informado é inválido.");
		}
	}

	/**
	 * Define o que será serializado ao codificar um JSON
	 */
	public function JsonSerialize() {
		return get_object_vars($this);
	}
}