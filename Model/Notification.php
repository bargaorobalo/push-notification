<?php

namespace PushNotification\Model;

/**
 * Notificação
 */
class Notification {
	protected $devices;
	protected $data;
	protected $message;

	/**
	 * Dispositivos
	 *
	 * @return Device[]
	 */
	public function getDevices() {
		return $this->devices;
	}

	/**
	 * Dados da notificação
	 *
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Mensagem
	 *
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Constructor
	 *
	 * @param Device[] $devices
	 *        	Identificador de push dos dispositivos que receberão a notificação
	 * @param string $message
	 *        	Mensagem a ser enviada
	 * @param array $data
	 *        	Dados extras
	 */
	public function __construct($devices, $message, $data) {
		if ((!$message && !$data)) {
			throw new \InvalidArgumentException("O conteúdo (mensagem ou dados extras) da notificação é obrigatório.");
		}

		$this->devices = $devices;
		$this->message = $message;
		$this->data = $data;
	}
}