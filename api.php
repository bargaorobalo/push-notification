<?php
require_once "vendor/autoload.php";
require_once "Model/Device.php";
require_once "Model/Notification.php";
require_once "Model/FailureDevice.php";
require_once "Model/GcmError.php";
require_once "Model/HttpStatusCode.php";
require_once "Model/NotificationResponse.php";
require_once "Push/DeviceManager.php";
require_once "Push/PushController.php";

use Slim\Slim;
use Unisuam\Model\Device;
use Unisuam\Model\Notification;
use Unisuam\Push\PushController;
use Unisuam\Push\DeviceManager;
use Unisuam\Model\HttpStatusCode;

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set("UTC");

echo "here";

// registra
Slim::registerAutoloader();

// inicializa e configura as rotas
$app = new Slim();
$app->post('/devices', 'createDevice');
$app->put('/devices', 'updateDevice');
$app->delete('/devices', "deleteDevice");
$app->post('/notifications', "sendNotification");
$app->run();

/**
 * Criação de dispositivo
 */
function createDevice() {
	$device = null;
	$app = Slim::getInstance();

	try {
		$device = getDeviceFromRequest($app->request());
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	print_r($device);

	try {
		DeviceManager::insertDevice($device);
		created("Dispositivo criado com sucesso.");
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Atualização de dispositivo
 */
function updateDevice() {
	$newRegistrationId = null;
	$device = null;
	$app = Slim::getInstance();

	try {
		// obtém os dados informados
		$request = $app->request();
		$input = json_decode($request->getBody());

		$device = getDeviceFromJson($input);
		$newRegistrationId = $input->new_registration_id;
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	try {
		DeviceManager::updateDevice($device, $newRegistrationId);
		noContent("Dispositivo atualizado com sucesso!");
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Remoção de dispositivo
 */
function deleteDevice() {
	$device = null;
	$app = Slim::getInstance();

	try {
		$device = getDeviceFromRequest($app->request());
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	try {
		DeviceManager::deleteDevice($device);
		noContent("Dispositivo removido com sucesso.");
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Envia uma notificação
 */
function sendNotification() {
	$notification = null;
	$app = Slim::getInstance();

	try {
		// leitura da notificação informado no post
		$request = $app->request();
		$input = json_decode($request->getBody());
		$devices = array();

		foreach($input->devices as $inputDevice) {
			$devices[] = getDeviceFromJson($inputDevice);
		}

		$notification = new Notification($devices, $input->data->message);
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	$pushController = new PushController();

	try {
		$notificationResult = $pushController->send($notification);
		$app->response()->header('Content-Type', 'application/json');
		echo json_encode($notificationResult);
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Retorna um dispositivo informado no request
 *
 * @param \Slim\Http\Request $request
 * @return \Unisuam\Model\Device dispositivo
 */
function getDeviceFromRequest($request) {
	$input = json_decode($request->getBody());
	return getDeviceFromJson($input);
}

/**
 * Retorna um dispositivo informado via json
 *
 * @param object $input
 *        	Json
 * @return Device dispositivo
 */
function getDeviceFromJson($input) {
	return new Device((string) $input->registration_id, (int) $input->type, (string) $input->user_id);
}

/**
 * Define
 *
 * @param int $statusCode
 *        	Código de status http
 * @param string $statusReason
 *        	Motivo do status http
 */
function setResponseStatus($statusCode, $statusReason) {
	$app = Slim::getInstance();
	$app->response()->status($statusCode);
	$app->response()->header('X-Status-Reason', $statusReason);
}

/**
 * Define
 *
 * @param int $statusCode
 *        	Código de status http
 * @param string $statusReason
 *        	Motivo do status http
 */
function created($statusReason) {
	setResponseStatus(HttpStatusCode::CREATED, $statusReason);
}

/**
 * Define o status como NO_CONTENT
 *
 * @param string $statusReason
 *        	Motivo do status http
 */
function noContent($statusReason) {
	setResponseStatus(HttpStatusCode::NO_CONTENT, $statusReason);
}

/**
 * Define o status com requisição inválida
 *
 * @param \Exception $exception Exceção ocorrida
 */
function badRequest($exception) {
	setResponseStatus(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
}

/**
 * Define o status com error no servidor
 *
 * @param \Exception $exception Exceção ocorrida
 */
function internalServerError($exception) {
	setResponseStatus(HttpStatusCode::INTERNAL_SERVER_ERROR, $exception->getMessage());
}

?>