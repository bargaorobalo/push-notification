<?php

namespace PushNotification\Model;

/**
 * Dispositivo para o qual não foi possível enviar uma notificação
 */
class FailureDevice extends Device {
	private $reason;

	/**
	 * Motivo da falha ao enviar a notificação para este dispositivo
	 *
	 * @return string
	 */
	public function getReason() {
		return $this->reason;
	}

	/**
	 * Constructor
	 *
	 * @param string $token
	 *        	Identificador de push do dispositivo para recebimento de notificações
	 * @param int $type
	 *        	Tipo do dispositivo
	 * @param string $reason
	 *        	Motivo da falha
	 */
	public function __construct($token, $type, $reason) {
		parent::__construct($token, $type, null);
		$this->reason = $reason;
	}

	/**
	 * Define o que será serializado ao codificar um JSON
	 */
	public function JsonSerialize() {
		return get_object_vars($this);
	}
}