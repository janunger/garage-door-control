<?php

namespace GDCBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use GDC\Sensor\Role;

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
     * @var \DateTime
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @param Role      $role
     * @param bool      $isOn
     * @param \DateTime $date
     */
    public function __construct(Role $role, $isOn, \DateTime $date)
    {
        $this->role = $role;
        $this->isOn = $isOn;
        $this->date = $date;
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
}

