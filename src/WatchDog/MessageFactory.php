<?php

declare(strict_types=1);

namespace JUIT\GDC\WatchDog;

class MessageFactory
{
    /**
     * @var string
     */
    private $senderAddress;

    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $recipientAddress;

    /**
     * @var string
     */
    private $recipientName;

    public function __construct(
        string $senderAddress,
        string $senderName,
        string $recipientAddress,
        string $recipientName
    ) {
        $this->senderAddress = $senderAddress;
        $this->senderName = $senderName;
        $this->recipientAddress = $recipientAddress;
        $this->recipientName = $recipientName;
    }

    public function createMessage(string $text): \Swift_Message
    {
        $message = new \Swift_Message($text, $text);
        $message->setFrom($this->senderAddress, $this->senderName);
        $message->setTo($this->recipientAddress, $this->recipientName);

        return $message;
    }
}
