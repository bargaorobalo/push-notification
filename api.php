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
require_once "Authorization/Authorization.php";

use Slim\Slim;
use PushNotification\Model\Device;
use PushNotification\Model\Notification;
use PushNotification\Model\HttpStatusCode;
use PushNotification\Push\PushController;
use PushNotification\Push\DeviceManager;
use PushNotification\Authorization\Authorization;

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set("UTC");

// registra
Slim::registerAutoloader();

// inicializa e configura as rotas
$app = new Slim();
$app->get	('/users/:userId/devices',	'authorize',	'getUserDevices');
$app->get	('/users', 					'authorize',	'getUsers');
$app->post	('/devices', 				'authorize',	'createDevice');
$app->put	('/devices',				'authorize',	'updateDevice');
$app->delete('/devices', 				'authorize',	'deleteDevice');
$app->post	('/notifications',			'authorize',	'sendNotification');
$app->run();

/**
 * Busca usuários que possuem dispositivos cadastrados
 *
 * Permite paginação através do parâmetros:
 * 	- page: página a ser retornada
 * 	- limit: quantidade de resultados a serem retornados
 */
function getUsers() {
	$app = Slim::getInstance();

	try {
		$request = $app->request();
		$page = (int) $request->params('page');
		$limit = (int) $request->params('limit');

		$users = DeviceManager::getUsersWithDevices($page, $limit);

		$app->response()->header('Content-Type', 'application/json');
		echo json_encode($users);
	} catch (\InvalidArgumentException $e) {
		badRequest($e);
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Busca de dispositivos de um usuário
 *
 * @param string $userId Identificador do usuário
 */
function getUserDevices($userId) {
	$app = Slim::getInstance();

	try {
		$devices = DeviceManager::getDevicesByUserId($userId);

		$app->response()->header('Content-Type', 'application/json');
		echo json_encode($devices);
	} catch (\InvalidArgumentException $e) {
		badRequest($e);
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Criação de dispositivo
 */
function createDevice() {
	$device = null;
	$app = Slim::getInstance();

	try {
		$input = json_decode($app->request()->getBody());
		$device = getDevice($input);
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	try {
		$deviceCreated = DeviceManager::insertDevice($device);

		if ($deviceCreated) {
			created("Dispositivo criado com sucesso.");
		} else {
			conflict("O dispositivo já está cadastrado");
		}
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Atualização de dispositivo
 */
function updateDevice() {
	$input = null;
	$app = Slim::getInstance();

	try {
		// obtém os dados informados
		$input = json_decode($app->request()->getBody());

		if (!$input || !isset($input->oldToken) || !isset($input->newToken) || !isset($input->userId)) {
			throw new \InvalidArgumentException("A requisição náo contém todos os dados necessários.");
		}
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	try {
		$updated = DeviceManager::updateDevice($input->oldToken, $input->newToken, $input->userId);

		if ($updated) {
			noContent("Dispositivo atualizado com sucesso!");
		} else {
			notFound("O dispositivo informado não foi encontrado.");
		}
	} catch (Exception $e) {
		internalServerError($e);
	}
}

/**
 * Remoção de dispositivo
 */
function deleteDevice() {
	$input = null;
	$app = Slim::getInstance();

	try {
		// obtém os dados informados
		$input = json_decode($app->request()->getBody());

		if (!$input || !isset($input->token)) {
			throw new \InvalidArgumentException("O identificador do dispositivo não foi informado.");
		}
	} catch (Exception $e) {
		badRequest($e);
		return;
	}

	try {
		if (DeviceManager::deleteDevice($input->token)) {
			noContent("Dispositivo removido com sucesso.");
		} else {
			notFound("O dispositivo informado não foi encontrado.");
		}
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
		$input = json_decode($app->request()->getBody());

		if (!$input || (!isset($input->message) && !isset($input->data)) || !isset($input->users)) {
			throw new \InvalidArgumentException("A requisição náo contém todos os dados necessários.");
		}

		$devices = array();
		$userIds = array();

		foreach($input->users as $item) {
			if (!isset($item->userId)) {
				throw new \InvalidArgumentException("A requisição não contém todos os dados necessários.");
			}

			$userIds[] = $item->userId;
		}

		$devices = DeviceManager::getDevicesByUsers($userIds);

		$message = isset($input->message) ? $input->message : null;
		$data = isset($input->data) ? json_decode(json_encode($input->data), true) : null;
		$notification = new Notification($devices, $message, $data);
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
 * Retorna um dispositivo informado, validando-o
 *
 * @param object $input
 *        	Dados informados
 * @return Device Dispositivo
 */
function getDevice($input) {
	if (!$input || !isset($input->token) || !isset($input->type) || !isset($input->userId)) {
		throw new \InvalidArgumentException("A requisição náo contém todos os dados necessários.");
	}

	$device = new Device((string) $input->token, (int) $input->type, (string) $input->userId);
	DeviceManager::validateDevice($device);
	return $device;
}


/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authorize(\Slim\Route $route) {
	$app = \Slim\Slim::getInstance();
	$headers = apache_request_headers();
	$authorizationHeader = $headers["Authorization"];

	// verifica se o token de acesso foi informado, se foi verifica se está possui acesso
	if (!isset($authorizationHeader) || !Authorization::isAuthorized($authorizationHeader)) {
		unauthorized();
	}
}

/**
 * Define o status como não autorizado e bloqueia a chamada da API
 */
function unauthorized() {
	// nenhuma aplicação encontrada com o token informado
	// token de acesso não informado no cabeçalho
	$app = Slim::getInstance();
	$app->response()->status(HttpStatusCode::UNAUTHORIZED);
	$app->response()->header('X-Status-Reason', "O acesso foi negado.");
	$app->stop();
}

/**
 * Define o status de resposta para a requisição
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
 * Define o status como resource criado
 *
 * @param string $statusReason
 *        	Motivo do status http
 */
function created($statusReason) {
	setResponseStatus(HttpStatusCode::CREATED, $statusReason);
}

/**
 * Define o status como OK, mas sem nada a retornar
 *
 * @param string $statusReason
 *        	Motivo do status http
 */
function noContent($statusReason) {
	setResponseStatus(HttpStatusCode::NO_CONTENT, $statusReason);
}

/**
 * Define o status como requisição inválida
 *
 * @param \Exception $exception
 *        	Exceção ocorrida
 */
function badRequest($exception) {
	setResponseStatus(HttpStatusCode::BAD_REQUEST, $exception->getMessage());
}

/**
 * Define o status como error no servidor
 *
 * @param \Exception $exception
 *        	Exceção ocorrida
 */
function internalServerError($exception) {
	setResponseStatus(HttpStatusCode::INTERNAL_SERVER_ERROR, $exception->getMessage());
}

/**
 * Define o status como conflito
 *
 * @param string $statusReason
 *        	Motivo do status http
 */
function conflict($statusReason) {
	setResponseStatus(HttpStatusCode::CONFLICT, $statusReason);
}

/**
 * Define o status como não encontrado
 *
 * @param string $statusReason
 *        	Motivo do status http
 */
function notFound($statusReason) {
	setResponseStatus(HttpStatusCode::NOT_FOUND, $statusReason);
}

?>