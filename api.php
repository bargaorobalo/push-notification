<?php
require_once "vendor/autoload.php";
require_once "Model/Device.php";
require_once "Model/Notification.php";
require_once "Model/FailureDevice.php";
require_once "Model/GcmError.php";
require_once "Model/NotificationResponse.php";
require_once "Push/DeviceManager.php";
require_once "Push/PushController.php";

use Slim\Slim;
use Unisuam\Model\Device;
use Unisuam\Model\Notification;
use Unisuam\Push\PushController;
use Unisuam\Push\DeviceManager;

error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set("UTC");

// registra
\Slim\Slim::registerAutoloader();

// inicializa e configura as rotas
$app = new \Slim\Slim();
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
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}

	try {
		DeviceManager::insertDevice($device);

		$app->response()->header('Content-Type', 'application/json');
		echo json_encode($device);
	} catch (Exception $e) {
		$app->response()->status(500);
		$app->response()->header('X-Status-Reason', $e->getMessage());
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
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}

	try {
		DeviceManager::updateDevice($device, $newRegistrationId);

		$app->response()->status(200);
		$app->response()->header('X-Status-Reason', "Dispositivo atualizado com sucesso!");
	} catch (Exception $e) {
		$app->response()->status(500);
		$app->response()->header('X-Status-Reason', $e->getMessage());
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
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
	}

	try {
		DeviceManager::deleteDevice($device);
	} catch (Exception $e) {
		$app->response()->status(500);
		$app->response()->header('X-Status-Reason', $e->getMessage());
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
		$app->response()->status(400);
		$app->response()->header('X-Status-Reason', $e->getMessage());
		return;
	}

	$pushController = new PushController();

	try {
		$notificationResult = $pushController->send($notification);
		$app->response()->header('Content-Type', 'application/json');
		echo json_encode($notificationResult);
	} catch (Exception $e) {
		$app->response()->status(500);
		$app->response()->header('X-Status-Reason', $e->getMessage());
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
?>