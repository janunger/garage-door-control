<?php

namespace GDCBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GDC\Sensor\Role;
use GDCBundle\Model\Microtime;

/**
 * @ORM\Table(name="sensor_log")
 * @ORM\Entity(repositoryClass="GDCBundle\Entity\SensorLogEntryRepository")
 */
class SensorLogEntry
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="role", type="string")
     */
    private $role;

    /**
     * @var bool
     * @ORM\Column(name="is_on", type="boolean")
     */
    private $isOn;

    /**
     * @var string
     *
     * @ORM\Column(name="microtime", type="bigint")
     */
    private $microtime;

    /**
     * @param Role      $role
     * @param bool      $isOn
     * @param Microtime $microtime
     */
    public function __construct(Role $role, $isOn, Microtime $microtime)
    {
        $this->role = $role;
        $this->isOn = $isOn;
        $this->microtime = $microtime->getValue();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return boolean
     */
    public function isOn()
    {
        return $this->isOn;
    }

    /**
     * @return Microtime
     */
    public function getMicrotime()
    {
        return new Microtime($this->microtime);
    }
}

