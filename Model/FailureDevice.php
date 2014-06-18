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
	 *        	Identificador do dispositivo para recebimento de notificaçõess
	 * @param int $type
	 *        	Tipo do dispositivo
	 */
	public function __construct($registrationId, $type, $reason) {
		parent::__construct($registrationId, $type);
		$this->reason = $reason;
	}
}