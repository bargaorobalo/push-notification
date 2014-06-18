<?php

namespace Unisuam\Push\Android;

use ZendService\Google\Gcm\Response;
use Sly\NotificationPusher\Adapter\Gcm as GcmAdapter;
use Sly\NotificationPusher\Model\Push;
use Unisuam\Model\FailureDevice;
use Unisuam\Model\GcmError;

/**
 * Envia notificações a um Android
 */
class AndroidPushController {

	/**
	 * Chave da api do ANDROID
	 *
	 * @var string
	 */
	const ANDROID_API_KEY = "AIzaSyApbMAGOln9XY4MgXFUD_RnqgoHv2jEt8M";

	/**
	 * Envia a notificação para dispositivos Android
	 *
	 * @param Device[] $devices
	 *        	Dispositivos
	 * @param Message $message
	 *        	Notificação
	 * @param NotificationResult $notificationResult
	 *        	Resultado do envio da notificação
	 * @param PushManager $pushManager
	 *        	Gerenciador de push
	 * @return object Registration id e motivo da falha para cada
	 *         dispositivo que não recebeu a notificação
	 */
	public static function send($devices, $message, $notificationResult, $pushManager) {
		if (iterator_count($devices->getIterator()) > 0) {
			try {
				$gcmAdapter = new GcmAdapter(array(
						'apiKey' => AndroidPushController::ANDROID_API_KEY
				));

				// envia as notificações
				$push = new Push($gcmAdapter, $devices, $message);
				$pushManager->add($push);
				$pushManager->push();

				// obtém a resposta do envio do envio
				$response = $gcmAdapter->getResponse();

				// dispositivo que não receberam as notificações e motivo
				$failureResults = $response->getResult(Response::RESULT_ERROR);
				$failureDevices = AndroidPushController::handleFailureResult($failureResults);

				// dispositivo que tiveram seus identificadores modificados
				$canonicalResults = $response->getResult(Response::RESULT_CANONICAL);
				AndroidPushController::handleCanonicalResult($canonicalResults);

				$notificationResult->addDevicesNotNotified($failureDevices);
			} catch (\Exception $e) {
				$notificationResult->setAndroidFailed(true);
				$notificationResult->setAndroidFailureReason($e->getMessage());
			}
		}
	}

	/**
	 * Trata os erros ocorridos ao enviar a notificação para o Android
	 *
	 * @param object $results
	 *        	Resultados de falha
	 * @return object Registration id e motivo da falha para cada
	 *         dispositivo que não recebeu a notificação
	 */
	private static function handleFailureResult($results) {
		$obj = new \ArrayObject($results);
		$iterator = $obj->getIterator();
		$failureDevices = array();

		// itera sobre os resultados de falha para verificar o motivo,
		// remove o dispositivo se o registration id não for mais válido
		while ($iterator->valid()) {
			$registrationId = $iterator->key();
			$reason = $iterator->current();

			if ($reason == GcmError::INVALID_REGISTRATION || $reason == GcmError::NOT_REGISTERED) {
				// TODO remover dispositivo do banco de dados.
			} else {
				$failureDevices[] = new FailureDevice($registrationId, Device::ANDROID, $reason);
			}

			$iterator->next();
		}

		return $failureDevices;
	}

	/**
	 * Trata os resultados contendo dispositivos Android que tiveram seus identificadores atualizados
	 *
	 * @param object $results
	 *        	Resultados
	 * @param int $deviceType
	 *        	Tipo de dispositivo
	 */
	private static function handleCanonicalResult($results) {
		$obj = new \ArrayObject($results);
		$iterator = $obj->getIterator();

		// itera sobre os resultados para atualizar os identificadores dos dispositivos
		while ($iterator->valid()) {
			$oldRegistrationId = $iterator->key();
			$newRegistraionId = $iterator->current()->registrationId;

			// TODO atualizar dispositivo

			$iterator->next();
		}
	}
}