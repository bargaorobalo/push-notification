<?php

namespace PushNotification\Model;

/**
 * Dispositivo
 *
 * @Entity
 * @Table(name="devices")
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

	/**
	 * @Id
	 * @Column(type="text")
	 */
	private $token;

	/**
	 * @Column(type="smallint")
	 */
	private $type;

	/**
	 * @Column(name="user_id", type="string")
	 */
	private $userId;

	/**
	 * Tipo do dispositivo
	 *
	 * @return int
	 */
	public function getDeviceType() {
		return $this->type;
	}

	/**
	 * Identificador de push do dispositivo para recebimento de notificações
	 *
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Define o identificador de push do dispositivo para o recebimento de notificações
	 *
	 * @param string $token Identificador de push
	 */
	public function setToken($token) {
		$this->token = $token;
	}

	/**
	 * Identificador do usuário
	 *
	 * @return string
	 */
	public function getUserId() {
		return $this->userId;
	}

	/**
	 * Define o identificador do usuário
	 *
	 * @param string $userId
	 */
	public function setUserId($userId) {
		$this->userId = $userId;
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
		$this->token = $token;
		$this->userId = $userId;
		$this->type = $type;
	}

	/**
	 * Define o que será serializado ao codificar um JSON
	 */
	public function JsonSerialize() {
		return get_object_vars($this);
	}
}