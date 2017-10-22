<?php

namespace GDCBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * @method SensorLogEntry|null find($id, $lockMode = \Doctrine\DBAL\LockMode::NONE, $lockVersion = null)
 * @method SensorLogEntry[]|\Doctrine\Common\Collections\ArrayCollection findAll()
 * @method SensorLogEntry[]|\Doctrine\Common\Collections\ArrayCollection findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method SensorLogEntry|null findOneBy(array $criteria, array $orderBy = null)
 */
class SensorLogEntryRepository extends EntityRepository
{
    public function save(SensorLogEntry $sensorLogEntry)
    {
        $this->_em->persist($sensorLogEntry);
        $this->_em->flush();
    }
}
