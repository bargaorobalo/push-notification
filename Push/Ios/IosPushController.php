<?php

namespace PushNotification\Push\Ios;

require_once 'vendor/zendframework/zendservice-apple-apns/library/ZendService/Apple/Exception/InvalidArgumentException.php';

use PushNotification\Push\DeviceManager;
use Sly\NotificationPusher\Adapter\Apns as ApnsAdapter;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\Adapter\Apns;
use Sly\NotificationPusher\PushManager;

/**
 * Controla o envio de notificações ao IOS
 */
class IosPushController {

	/**
	 * Envia a notificação para dispositivos IOS
	 *
	 * @param Device[] $devices
	 *        	Dispositivos
	 * @param Message $message
	 *        	Notificação
	 * @param NotificationResult $notificationResult
	 *        	Resultado do envio da notificação
	 * @param PushManager $pushManager
	 *        	Gerenciador de push
	 */
	public static function send($devices, $message, $notificationResult, $pushManager) {
		$certificate = null;
		$certificatePassword = null;

		// irá lançar exceção se o certificado não existir
		$apnsAdapter = new ApnsAdapter(array(
				'certificate' => IOS_CERTIFICATE_PATH,
				'passPhrase' => IOS_CERTIFICATE_PASSWORD
		));

		if (iterator_count($devices->getIterator()) > 0) {
			try {
				$push = new Push($apnsAdapter, $devices, $message);
				$pushManager->add($push);
				$pushManager->push();
			} catch (\Exception $e) {
				global $log;
				$log->Error($e);
				$notificationResult->setIosFailed(true);
				$notificationResult->setIosFailureReason($e->getMessage());
			}
		}

		IosPushController::getFeedback($pushManager, $apnsAdapter);
	}

	/**
	 * Obtém o feedback do IOS (lista de token que não são mais válidos,
	 * pois o dispositivo não está mais registrado)
	 *
	 * @param PushManager $pushManager
	 *        	Gerenciado de push
	 * @param Apns $apnsAdapter
	 *        	Adaptador para envio de push ao IOS
	 */
	private static function getFeedback($pushManager, $apnsAdapter) {
		try {
			// obtém os dispositivos que foram desregistrados e remove-os
			$unregisteredTokens = $pushManager->getFeedback($apnsAdapter);

			if ($unregisteredTokens) {
				foreach($unregisteredTokens as $token) {
					DeviceManager::deleteDevice($token);
				}
			}
		} catch (\Exception $e) {
			global $log;
			$log->Warn($e);
			// nada a fazer, irá tentar novamente na próxima vez que enviar notificações
		}
	}
}