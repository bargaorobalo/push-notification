<?php

namespace PushNotification\Push\Ios;

require_once 'vendor/zendframework/zendservice-apple-apns/library/ZendService/Apple/Exception/InvalidArgumentException.php';

use PushNotification\Model\FailureDevice;
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
	 * Caminho para o arquivo do certificado do IOS para produção
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PATH_PROD = 'apns-certificate.pem';

	/**
	 * Senha para o certificado do IOS para produção
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PASSWORD_PROD = "passprod";

	/**
	 * Caminho para o arquivo do certificado do IOS para desenvolvimento
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PATH_DEV = 'apns-certificate-dev.pem';

	/**
	 * Senha para o certificado do IOS para desenvolvimento
	 *
	 * @var string
	 */
	const IOS_CERTIFICATE_PASSWORD_DEV = "passdev";

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
	 * @param string $environment
	 *        	Ambiente a ser utilizado, possíveis valores:
	 *        	PushManager::ENVIRONMENT_DEV, PushManager:ENVIRONMENT_PROD.
	 */
	public static function send($devices, $message, $notificationResult, $pushManager, $environment) {
		if (iterator_count($devices->getIterator()) > 0) {
			try {
				$certificate = null;
				$certificatePassword = null;

				if ($environment == PushManager::ENVIRONMENT_PROD) {
					$certificate = IosPushController::IOS_CERTIFICATE_PATH_PROD;
					$certificatePassword = IosPushController::IOS_CERTIFICATE_PASSWORD_PROD;
				} else {
					$certificate = IosPushController::IOS_CERTIFICATE_PATH_DEV;
					$certificatePassword = IosPushController::IOS_CERTIFICATE_PASSWORD_DEV;
				}

				// irá lançar exceção se o certificado não existir
				$apnsAdapter = new ApnsAdapter(array(
						'certificate' => $certificate,
						'passPhrase' => $certificatePassword
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
					DeviceManager::deleteDevice($token);
				}
			}

			$notificationResult->addDevicesNotNotified($failureDevices);
		} catch (\Exception $e) {
			// nada a fazer, irá tentar novamente na próxima vez que enviar notificações
		}
	}
}