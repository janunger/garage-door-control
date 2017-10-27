<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

class Repository
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $fetchStatement;

    /**
     * @var \PDOStatement
     */
    private $deleteStatement;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return SequenceItem[] */
    public function getSequence(): array
    {
        $commands  = [];
        $statement = $this->getFetchStatement();
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $commands[] = new SequenceItem(
                (int) $row['id'],
                new Command($row['command'])
            );
        }

        return $commands;
    }

    public function delete(SequenceItem $sequenceItem)
    {
        $this->getDeleteStatement()->execute([$sequenceItem->getId()]);
    }

    private function getFetchStatement(): \PDOStatement
    {
        if (null === $this->fetchStatement) {
            $this->fetchStatement = $this->pdo->prepare('SELECT id, command FROM command_queue ORDER BY id ASC');
        }

        return $this->fetchStatement;
    }

    private function getDeleteStatement(): \PDOStatement
    {
        if (null === $this->deleteStatement) {
            $this->deleteStatement = $this->pdo->prepare('DELETE FROM command_queue WHERE id = ?');
        }

        return $this->deleteStatement;
    }
}
