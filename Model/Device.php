<?php

namespace Unisuam\Model;

/**
 * Dispositivo 
 */
class Device
{
    /**
     * Dispositivo android
     */
    const ANDROID = 1;

    /**
     * Dispositivo IOS
     */
    const IOS = 2;

    protected $registrationId;
    protected $type;
    protected $userId;

    /**
     * Tipo do dispositivo
     * 
     * @return int
     */
    public function getDeviceType()
    {
        return $this->type;
    }

    /**
     * Identificador do dispositivo para recebimento de notificações
     * 
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     * Constructor
     *
     * @param string $registrationId Identificador do dispositivo para recebimento de notificações
     * @param int $type Tipo do dispositivo
     */
    public function __construct($registrationId, $type)
    {
        if (!$registrationId) {
            throw new \InvalidArgumentException("O identificador do dispositivo é obrigatório.");
        }

        if (!$type) {
            throw new \InvalidArgumentException("O tipo do dispositivo é obrigatório.");
        }

        $this->registrationId = $registrationId;

        if ($type === Device::ANDROID || $type === Device::IOS) {
            $this->type = $type;
        } else {
            throw new \InvalidArgumentException("O tipo de dispositivo informado é inválido.");
        }
    }
}