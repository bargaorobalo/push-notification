<?php

namespace PushNotification\Model;

/**
 * Resposta do envio de notificação
 */
class NotificationResponse implements \JsonSerializable {
	private $androidFailed = false;
	private $iosFailed = false;
	private $androidFailureReason = null;
	private $iosFailureReason = null;
	private $devicesNotNotified = null;

	/**
	 * Define se houve uma falha geral ao enviar notificações para o android.
	 *
	 * @param bool $failed
	 *        	True se falhou totalmente, false caso contrário
	 */
	public function setAndroidFailed($failed) {
		$this->androidFailed = $failed;
	}

	/**
	 * Indica se houve uma falha geral ao enviar notificações para o android.
	 *
	 * Um erro geral ocorre se não for possível acessar o serviço ou se um ou mais
	 * identificadores forem inválidos.
	 *
	 * return bool True se falhou totalmente, false caso contrário
	 */
	public function hasAndroidFailed() {
		return $this->androidFailed;
	}

	/**
	 * Define se houve uma falha geral ao enviar notificações para o IOS.
	 *
	 * @param bool $failed
	 *        	True se falhou totalmente, false caso contrário
	 */
	public function setIosFailed($failed) {
		$this->iosFailed = $failed;
	}

	/**
	 * Indica se houve uma falha geral ao enviar notificações para o IOS.
	 *
	 * Um erro geral ocorre se não for possível acessar o serviço, se um ou mais
	 * identificadores forem inválidos ou se o certificado informado for inválido.
	 *
	 * return bool True se falhou totalmente, false caso contrário
	 */
	public function hasIosFailed() {
		return $this->iosFailed;
	}

	/**
	 * Define o motivo da falha geral ocorrida no envio de notificações para o android
	 *
	 * @param string $reason
	 *        	Motivo da falha geral
	 */
	public function setAndroidFailureReason($reason) {
		$this->androidFailureReason = $reason;
	}

	/**
	 * Motivo da falha geral no envio de notificações para o android
	 *
	 * @return string
	 */
	public function getAndroidFailureReason() {
		return $this->androidFailureReason;
	}

	/**
	 * Define o motivo da falha geral ocorrida no envio de notificações para o IOS
	 *
	 * @param string $reason
	 *        	Motivo da falha geral
	 */
	public function setIosFailureReason($reason) {
		$this->iosFailureReason = $reason;
	}

	/**
	 * Motivo da falha geral no envio de notificações para o IOS
	 *
	 * @return string
	 */
	public function getIosFailureReason() {
		return $this->iosFailureReason;
	}

	/**
	 * Dispositivos para os quais não foi possível enviar a notificação
	 *
	 * @return FailureDevice[]
	 */
	public function getDevicesNotNotified() {
		return $this->devicesNotNotified;
	}

	/**
	 * Define os dispositivos para os quais não foi possível enviar a notificação
	 *
	 * @param FailureDevices[] $devices
	 *        	Dispositivos para os quais não foi possível enviar as notificações
	 */
	public function addDevicesNotNotified($devices) {
		if (!$this->devicesNotNotified) {
			$this->devicesNotNotified = array();
		}

		$this->devicesNotNotified = array_merge($this->devicesNotNotified, $devices);
	}

	/**
	 * Define o que será serializado ao codificar um JSON
	 */
	public function JsonSerialize() {
		return get_object_vars($this);
	}
}