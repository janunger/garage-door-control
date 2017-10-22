<?php

namespace GDCBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CommandQueueEntryRepository extends EntityRepository
{
    /**
     * @return CommandQueueEntry[]
     */
    public function getList()
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')->orderBy('c.id', 'ASC');
        $query = $qb->getQuery();

        return $query->execute();
    }

    public function save(CommandQueueEntry $queueEntry)
    {
        $this->_em->persist($queueEntry);
        $this->_em->flush();
    }

    public function delete(CommandQueueEntry $queueEntry)
    {
        $this->_em->remove($queueEntry);
        $this->_em->flush();
    }
}
