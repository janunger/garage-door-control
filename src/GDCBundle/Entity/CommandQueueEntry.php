<?php

namespace GDCBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use GDC\CommandQueue\Command;

/**
 * @ORM\Entity(repositoryClass="GDCBundle\Entity\CommandQueueEntryRepository")
 * @ORM\Table(name="command_queue", options={"engine"="memory"})
 */
class CommandQueueEntry
{
    /**
     * @var integer|null
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = null;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string")
     */
    private $command;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct(Command $command)
    {
        $this->command   = $command->getValue();
        $this->createdAt = new DateTime();
    }

    /**
     * @return integer|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Command
     */
    public function getCommand()
    {
        return new Command($this->command);
    }
}
