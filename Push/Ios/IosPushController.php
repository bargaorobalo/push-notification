<?php

namespace PushNotification\Push\Ios;

// FIXME por algum motivo não estava sendo carregada automaticamente
require_once 'vendor/zendframework/zendservice-apple-apns/library/ZendService/Apple/Exception/InvalidArgumentException.php';

use PushNotification\Model\FailureDevice;
use Sly\NotificationPusher\Adapter\Apns as ApnsAdapter;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\Adapter\Apns;

class IosPushController {
	/**
	 * Caminho para o arquivo do certificado do IOS
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PATH = "ck.pem"; // 'apns-certificate.pem';

	/**
	 * Senha para o certificado do IOS
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PASSWORD = "141707";

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
		if (iterator_count($devices->getIterator()) > 0) {
			try {
				// irá lançar exceção se o certificado não existir
				$apnsAdapter = new ApnsAdapter(array(
						'certificate' => IosPushController::IOS_CERTIFICATE_PATH,
						'passPhrase' => IosPushController::IOS_CERTIFICATE_PASSWORD
				));

				$push = new Push($apnsAdapter, $devices, $message);
				$pushManager->add($push);
				$pushManager->push();
			} catch (\Exception $e) {
				$notificationResult->setIosFailed(true);
				$notificationResult->setIosFailureReason($e->getMessage());
			}
		}

		IosPushController::getFeedback($notificationResult, $pushManager, $apnsAdapter);
	}

	/**
	 * Obtém o feedback do IOS (lista de token que não são mais válidos,
	 * pois o dispositivo não está mais registrado)
	 *
	 * @param NotificationResult $notificationResult
	 *        	Resultado da notificação
	 * @param PushManager $pushManager
	 *        	Gerenciado de push
	 * @param Apns $apnsAdapter
	 *        	Adaptador para envio de push ao IOS
	 */
	private static function getFeedback($notificationResult, $pushManager, $apnsAdapter) {
		try {
			// obtém os dispositivos que foram desregistrados e remove-os
			$unregisteredTokens = $pushManager->getFeedback($apnsAdapter);
			$failureDevices = array();

			if ($unregisteredTokens) {
				foreach($unregisteredTokens as $token) {
					// TODO remover do banco
				}
			}

			$notificationResult->addDevicesNotNotified($failureDevices);
		} catch (\Exception $e) {
			// nada a fazer, irá tentar novamente na próxima vez que enviar notificações
		}
	}
}