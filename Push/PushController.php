<?php
namespace Unisuam\Push;

use Unisuam\Model\Device;
use Unisuam\Model\NotificationResponse;
use Unisuam\Model\GcmError;
use Unisuam\Model\FailureDevice;
use Sly\NotificationPusher\PushManager;
use Sly\NotificationPusher\Adapter\Apns as ApnsAdapter;
use Sly\NotificationPusher\Adapter\Gcm as GcmAdapter;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device as PusherDevice;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use ZendService\Google\Gcm\Response;

/**
 * Controlado do push
 *
 * Gerencia o envio de mensagens via push
 */
class PushController
{
    /**
     * Chave da api do ANDROID
     */
    const ANDROID_API_KEY = "AIzaSyApbMAGOln9XY4MgXFUD_RnqgoHv2jEt8M"; 

    /**
     * Caminho para o arquivo do certificado do IOS
     */
    const IOS_CERTIFICATE_PATH = 'apns-certificate.pem';

    /**
     * Gerenciador do push
     * 
     * @var PushManager
     */
    protected $pushManager;

    /**
     * Adaptador que envia notificações para dispositivos android
     * 
     * @var Gcm
     */
    protected $gcmAdapter;

    /**
     * Adaptador que envia notificações para dispositivos IOS
     * 
     * @var Apns
     */
    protected $apnsAdapter;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        $this->gcmAdapter = new GcmAdapter(array(
            'apiKey' => PushController::ANDROID_API_KEY
        ));

        //irá lançar exceção se o certificado não existir
        $this->apnsAdapter = new ApnsAdapter(array(
            'certificate' => PushController::IOS_CERTIFICATE_PATH
        ));
    }

    /**
     * Envia a notificação
     *      
     * @param Notification $notification Notificação a ser enviada
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
        foreach ($notification->getDevices() as $device) {
            switch($device->getDeviceType()) {
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
        $this->sendToAndroid($androidDevices, $message, $notificationResult);                  
        $this->sendToIos($iosDevices, $message, $notificationResult);                          

        return $notificationResult;
    }

    /**
     * Envia a notificação para dispositivos Android
     * 
     * @param  Device[] $devices Dispositivos
     * @param  Message  $message Notificação
     * @param  NotificationResult $notificationResult Resultado do envio da notificação
     * @return object   Registration id e motivo da falha para cada 
     *                  dispositivo que não recebeu a notificação
     */
    private function sendToAndroid($devices, $message, $notificationResult) {     
        if (iterator_count($devices->getIterator()) > 0) {
            try {            
                // envia as notificações
                $push = new Push($this->gcmAdapter, $devices, $message);
                $this->pushManager->add($push);
                $this->pushManager->push();

                // obtém a resposta do envio do envio
                $response = $this->gcmAdapter->getResponse();

                // dispositivo que não receberam as notificações e motivo
                $failureResults = $response->getResult(Response::RESULT_ERROR);        
                $failureDevices = $this->handleFailureResult($failureResults, Device::ANDROID);
                
                // dispositivo que tiveram seus identificadores modificados
                $canonicalResults = $response->getResult(Response::RESULT_CANONICAL);
                $this->handleCanonicalResult($canonicalResults, Device::IOS);

                $notificationResult->addDevicesNotNotified($failureDevices); 
            } catch (\Exception $e) {
                $notificationResult->setAndroidFailed(true);
                $notificationResult->setAndroidFailureReason($e->getMessage());
            }  
        }
    }

    /**
     * Envia a notificação para dispositivos IOS
     * 
     * @param  Device[] $devices Dispositivos
     * @param  Message  $message Notificação
     * @param  NotificationResult $notificationResult Resultado do envio da notificação
     * @return object   Registration id e motivo da falha para cada 
     *                  dispositivo que não recebeu a notificação
     */
    private function sendToIos($devices, $message, $notificationResult) {
        //TODO verifica envio ao IOS e tratar retornos
        
        if (iterator_count($devices->getIterator()) > 0) {
            try {
                $push = new Push($this->apnsAdapter, $devices, $message);
                $this->pushManager->add($push);
                $this->pushManager->push();

                $feedback = $pushManager->getFeedback($this->apnsAdapter);
                print_r($feedback->getResults());

                //$notificationResult->addDevicesNotNotified($failureDevices);
            } catch (\Exception $e) {
                $notificationResult->setIosFailed(true);
                $notificationResult->setIosFailureReason($e->getMessage());
            }  
        }
    }
    
    /**
     * Trata os erros ocorridos ao enviar a notificação
     * 
     * @param  object $results Resultados de falha
     * @param  int $deviceType Tipo de dispositivo
     * @return object  Registration id e motivo da falha para cada 
     *                 dispositivo que não recebeu a notificação
     */
    private function handleFailureResult($results, $deviceType) {
        $obj = new \ArrayObject( $results );
        $iterator = $obj->getIterator();
        $failureDevices = array();

        // itera sobre os resultados de falha para verificar o motivo,
        // remove o dispositivo se o registration id não for mais válido
        while( $iterator->valid() )
        {
            $registrationId = $iterator->key();
            $reason = $iterator->current();

            if ($reason == GcmError::INVALID_REGISTRATION || $reason == GcmError::NOT_REGISTERED) {
                //TODO remover dispositivo do banco de dados.
            } else {
                $failureDevices[] = new FailureDevice($registrationId, $deviceType, $reason);
            }

            $iterator->next();
        }

        return $failureDevices;
    }
    
    /**
     * Trata os resultados contendo dispositivos que tiveram seus identificadores atualizados
     * 
     * @param  object $results Resultados
     * @param  int $deviceType Tipo de dispositivo
     */
    private function handleCanonicalResult($results, $deviceType) {
        $obj = new \ArrayObject( $results );
        $iterator = $obj->getIterator();

        // itera sobre os resultados para atualizar os identificadores dos dispositivos
        while( $iterator->valid() )
        {
            $oldRegistrationId = $iterator->key();
            $newRegistraionId = $iterator->current()->registrationId;

            //TODO atualizar dispositivo

            $iterator->next();
        }
    }
}