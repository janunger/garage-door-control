<?php

namespace GDCBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * @method DoorState|null find($id, $lockMode = \Doctrine\DBAL\LockMode::NONE, $lockVersion = null)
 * @method DoorState[]|\Doctrine\Common\Collections\ArrayCollection findAll()
 * @method DoorState[]|\Doctrine\Common\Collections\ArrayCollection findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method DoorState|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoorStateRepository extends EntityRepository
{
    public function save(DoorState $queueEntry)
    {
        $this->_em->persist($queueEntry);
        $this->_em->flush();
    }

    public function delete(DoorState $queueEntry)
    {
        $this->_em->remove($queueEntry);
        $this->_em->flush();
    }
}
