<?php

namespace Unisuam\Model;

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
	 * @param string $registrationId
	 *        	Identificador do dispositivo para recebimento de notificaçõess *
	 * @param int $type
	 *        	Tipo do dispositivo
	 * @param string $userId
	 *        	Identificador do usuário
	 * @param string $reason
	 *        	Motivo da falha
	 */
	public function __construct($registrationId, $type, $userId, $reason) {
		parent::__construct($registrationId, $type, $userId);
		$this->reason = $reason;
	}
}