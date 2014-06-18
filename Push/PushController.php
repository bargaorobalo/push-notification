<?php

namespace Unisuam\Push;

require_once "Model/GcmError.php";
require_once "Push/Android/AndroidPushController.php";
require_once "Push/Ios/IosPushController.php";

use Unisuam\Model\Device;
use Unisuam\Model\NotificationResponse;
use Sly\NotificationPusher\PushManager;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device as PusherDevice;
use Sly\NotificationPusher\Model\Message;
use Unisuam\Push\Android\AndroidPushController;
use Unisuam\Push\Ios\IosPushController;

/**
 * Controlado do push
 *
 * Gerencia o envio de mensagens via push
 */
class PushController {
	/**
	 * Gerenciador do push
	 *
	 * @var PushManager
	 */
	protected $pushManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);
	}

	/**
	 * Envia a notificação
	 *
	 * @param Notification $notification
	 *        	Notificação a ser enviada
	 * @return NotificationResult Resultado do envio da notificação
	 */
	public function send($notification) {
		// verifica a notificação
		if (!$notification) {
			throw new \InvalidArgumentException("A notificação não foi informada ou é inválida.");
		}

		$iosDevices = new DeviceCollection();
		$androidDevices = new DeviceCollection();
		$message = new Message(json_encode($notification->getData()));

		// separa os dispositivos por tipo
		foreach($notification->getDevices() as $device) {
			switch ($device->getDeviceType()) {
				case Device::ANDROID:
					$androidDevices->add(new PusherDevice($device->getRegistrationId()));
					break;
				case Device::IOS:
					$iosDevices->add(new PusherDevice($device->getRegistrationId()));
					break;
			}
		}

		$notificationResult = new NotificationResponse();

		// envia as notificações
		AndroidPushController::send($androidDevices, $message, $notificationResult, $this->pushManager);
		IosPushController::send($iosDevices, $message, $notificationResult, $this->pushManager);

		return $notificationResult;
	}
}