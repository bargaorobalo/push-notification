<?php
	require_once "vendor/autoload.php";
	require_once "Model/Device.php";
	require_once "Model/Notification.php";
	require_once "Model/FailureDevice.php";
	require_once "Model/GcmError.php";
	require_once "Model/NotificationResponse.php";
	require_once "Push/PushController.php";
	
	use Slim\Slim;
	use Unisuam\Model\Device;
	use Unisuam\Model\Notification;    
	use Unisuam\Push\PushController;
	
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	date_default_timezone_set("UTC");
	
	// registra
	\Slim\Slim::registerAutoloader();
	
	// inicializa e configura as rotas
	$app = new \Slim\Slim();
	
	/**
	 * Criação de dispositivos
	 */
	$app->post('/devices', function() use ($app) {
	    try {
	        // get and decode JSON request body
	        $request = $app->request();
	        $input = json_decode($request->getBody()); 
	
			// TODO receber identificador do usuário
	        // cria o dispositivo
	        $device = new Unisuam\Model\Device((string)$input->registration_id, (int)$input->type);
	
	        //TODO armazenar dispositivo em um banco
	
	        $app->response()->header('Content-Type', 'application/json');
	        echo json_encode($device);
	    } catch (Exception $e) {
	        $app->response()->status(400);
	        $app->response()->header('X-Status-Reason', $e->getMessage());
	    }
	});
	
	/**
	 * Envio de notificação
	 */
	$app->post('/notifications', function() use ($app) {
	    $notification = null;
	    	
	    try {
	        //leitura da notificação informado no post
	        $request = $app->request();
	        $input = json_decode($request->getBody()); 
	        $devices = array();
	
	        foreach($input->devices as $inputDevice) {
	            $devices[] = new Device($inputDevice->registration_id, $inputDevice->type);                
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
	});
	
	$app->run();
?>