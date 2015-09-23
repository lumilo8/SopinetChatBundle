<?php
namespace Sopinet\ChatBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DeviceRepository extends EntityRepository
{
    /**
     * Comprueba si existe un dispositivo en la base de datos
     *
     * @param String $deviceId
     *
     * @return bool
     */
    public function existsDevice($deviceId)
    {
        return $this->findOneByDeviceId($deviceId)!=null;
    }

    public function supportsClass($class)
    {
        return $class === 'Sopinet\ChatBundle\Entity\Device';
    }
}
?>