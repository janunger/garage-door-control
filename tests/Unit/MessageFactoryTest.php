<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Unit;

use JUIT\GDC\WatchDog\MessageFactory;
use PHPUnit\Framework\TestCase;

class MessageFactoryTest extends TestCase
{
    /** @var MessageFactory */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();
        $this->SUT = new MessageFactory('sender@example.com', 'Sender', 'recipient@example.com', 'Recipient');
    }

    /** @test */
    public function it_creates_a_message_with_the_expected_data()
    {
        $message = $this->SUT->createMessage('The message text');

        static::assertSame(['sender@example.com' => 'Sender'], $message->getFrom());
        static::assertSame(['recipient@example.com' => 'Recipient'], $message->getTo());
        static::assertSame('The message text', $message->getSubject());
        static::assertSame('The message text', $message->getBody());
    }
}
