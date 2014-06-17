<?php
namespace Unisuam\Model;

/**
 * Notificação
 */
class Notification
{
    protected $devices;
    protected $data;

    /**
     * Dispositivos
     * 
     * @return Device[]
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
      * Dados da notificação
      * 
      * @return object
      */
    public function getData()
    {
        return $this->data;
    }

	/**
     * Constructor
     *
     * @param string $devices Identificador do dispositivo para recebimento de notificaçõess
     * @param int $type Tipo do dispositivo
     */
    public function __construct($devices, $data)
    {
        if (!$devices || !$data) {
            throw new \InvalidArgumentException("Os dispositivos e o conteúdo da notificação são obrigatórios.");
        }

        $this->devices = $devices;
        $this->data = $data; 
    }
}