<?php

declare(strict_types=1);

namespace JUIT\GDC;

use JUIT\GDC\Door\Door;
use JUIT\GDC\Door\DoorInterface;
use JUIT\GDC\Model\InputPinIdDoorClosed;
use JUIT\GDC\Model\InputPinIdDoorOpened;
use JUIT\GDC\WatchDog\DoorStateWriter;
use JUIT\GDC\WatchDog\MessageFactory;
use JUIT\GDC\WatchDog\Messenger;
use JUIT\GDC\WatchDog\WatchDog;
use JUIT\PiFace\Emulator\OutputPin as OutputPinEmulator;
use JUIT\PiFace\Emulator\PiFace as PiFaceEmulator;
use JUIT\PiFace\Hardware\OutputPin as OutputPinHardware;
use JUIT\PiFace\Hardware\PiFace as PiFaceHardware;
use JUIT\PiFace\OutputPin;
use JUIT\PiFace\PiFace;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceContainer
{
    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    private function getBaseDir(): string
    {
        return $this->config['app']['base_dir'];
    }

    private function usePifaceHardware(): bool
    {
        return $this->config['piface']['use_hardware'] === true;
    }

    private function createActorMotorHardware(): OutputPin
    {
        return new OutputPinHardware($this->config['piface']['pin_id_motor_trigger']);
    }

    private function createActorMotorEmulator(): OutputPin
    {
        return new OutputPinEmulator();
    }

    private function getActorMotor(): OutputPin
    {
        static $instance = null;
        if (null === $instance) {
            $instance = $this->usePifaceHardware()
                ? $this->createActorMotorHardware()
                : $this->createActorMotorEmulator();
        }

        return $instance;
    }

    private function createPiFaceHardware(): PiFace
    {
        return new PiFaceHardware();
    }

    private function createPiFaceEmulator(): PiFace
    {
        return new PiFaceEmulator(new \SplFileInfo($this->getBaseDir() . '/var/emulator/emulator'));
    }

    public function getPiFace(): PiFace
    {
        static $instance = null;
        if (null === $instance) {
            $instance = $this->usePifaceHardware() ? $this->createPiFaceHardware() : $this->createPiFaceEmulator();
        }

        return $instance;
    }

    private function getDoor(): DoorInterface
    {
        static $instance = null;
        if (null === $instance) {
            $config   = $this->config['piface'];
            $instance = new Door(
                $this->getPiFace(),
                new InputPinIdDoorClosed($config['pin_id_closed']),
                new InputPinIdDoorOpened($config['pin_id_opened']),
                $this->getActorMotor()
            );
        }

        return $instance;
    }

    private function getMailerTransport(): \Swift_SmtpTransport
    {
        static $instance = null;
        if (null === $instance) {
            $config = $this->config['mailer'];

            $instance = new \Swift_SmtpTransport($config['host'], $config['port']);
            if (null !== $config['encryption']) {
                $instance->setEncryption($config['encryption']);
            }
            if (null !== $config['username']) {
                $instance->setUsername($config['username']);
            }
            if (null !== $config['password']) {
                $instance->setPassword($config['password']);
            }
            if (null !== $config['auth_mode']) {
                $instance->setAuthMode($config['auth_mode']);
            }
        }

        return $instance;
    }

    private function getMailer(): \Swift_Mailer
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new \Swift_Mailer($this->getMailerTransport());
        }

        return $instance;
    }

    private function getMessageFactory(): MessageFactory
    {
        static $instance = null;
        if (null === $instance) {
            $config   = $this->config['mailer'];
            $instance = new MessageFactory(
                $config['sender_address'],
                $config['sender_name'],
                $config['recipient_address'],
                $config['recipient_name']
            );
        }

        return $instance;
    }

    private function getMessenger(): Messenger
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new Messenger($this->getMailer(), $this->getMessageFactory());

            $eventDispatcher = $this->getEventDispatcher();
            $eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::RESTARTED, [$instance, 'onWatchdogRestart']);
            $eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::DOOR_OPENING, [$instance, 'onDoorOpening']);
            $eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::DOOR_CLOSED, [$instance, 'onDoorClosed']);
            $eventDispatcher->addListener(
                \JUIT\GDC\Event\WatchDogEvents::HARDWARE_ERROR,
                [$instance, 'onHardwareError']
            );
        }

        return $instance;
    }

    private function getEventDispatcher(): EventDispatcherInterface
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new EventDispatcher();
        }

        return $instance;
    }

    private function getDoorStateWriter(): DoorStateWriter
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new DoorStateWriter(new \SplFileInfo($this->getBaseDir() . '/public/state/current.json'));
        }

        return $instance;
    }

    private function getWatchDog(): WatchDog
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new WatchDog(
                $this->getDoor(),
                $this->getMessenger(),
                $this->getEventDispatcher(),
                $this->getDoorStateWriter()
            );
        }

        return $instance;
    }

    public function getEventLoop(): EventLoop
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new EventLoop($this->getWatchDog(), $this->getMailerTransport());
        }

        return $instance;
    }
}
