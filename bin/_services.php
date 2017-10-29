<?php

declare(strict_types=1);

$config = require __DIR__ . '/../etc/config.php';

if ($config['piface']['use_hardware']) {
    $piFace                  = new \JUIT\PiFace\Hardware\PiFace();
    $actorMotor              = new \JUIT\PiFace\Hardware\OutputPin($config['piface']['pin_id_motor_trigger']);
} else {
    $emulatorDataFile = new SplFileInfo(__DIR__ . '/../var/emulator/emulator');
    $piFace           = new \JUIT\PiFace\Emulator\PiFace($emulatorDataFile);
    $actorMotor       = new \JUIT\PiFace\Emulator\OutputPin();
}

$door = new \JUIT\GDC\Door\Door(
    $piFace,
    new \JUIT\GDC\Model\InputPinIdDoorClosed($config['piface']['pin_id_closed']),
    new \JUIT\GDC\Model\InputPinIdDoorOpened($config['piface']['pin_id_opened']),
    $actorMotor
);

$transport = new Swift_SmtpTransport($config['mailer']['host'], $config['mailer']['port']);
if (null !== $config['mailer']['encryption']) {
    $transport->setEncryption($config['mailer']['encryption']);
}
if (null !== $config['mailer']['username']) {
    $transport->setUsername($config['mailer']['username']);
}
if (null !== $config['mailer']['password']) {
    $transport->setPassword($config['mailer']['password']);
}
if (null !== $config['mailer']['auth_mode']) {
    $transport->setAuthMode($config['mailer']['auth_mode']);
}

$swiftMailer      = new Swift_Mailer($transport);
$senderAddress    = $config['mailer']['sender_address'];
$senderName       = $config['mailer']['sender_name'];
$recipientAddress = $config['mailer']['recipient_address'];
$recipientName    = $config['mailer']['recipient_name'];
$messageFactory   = new \JUIT\GDC\WatchDog\MessageFactory(
    $senderAddress, $senderName, $recipientAddress, $recipientName
);
$messenger        = new \JUIT\GDC\WatchDog\Messenger($swiftMailer, $messageFactory);

$eventDispatcher = new Symfony\Component\EventDispatcher\EventDispatcher();
$eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::RESTARTED, [$messenger, 'onWatchdogRestart']);
$eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::DOOR_OPENING, [$messenger, 'onDoorOpening']);
$eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::DOOR_CLOSED, [$messenger, 'onDoorClosed']);
$eventDispatcher->addListener(\JUIT\GDC\Event\WatchDogEvents::HARDWARE_ERROR, [$messenger, 'onHardwareError']);

$doorStateWriter = new \JUIT\GDC\WatchDog\DoorStateWriter(new SplFileInfo(__DIR__ . '/../public/state/current.json'));

$watchDog = new \JUIT\GDC\WatchDog\WatchDog($door, $messenger, $eventDispatcher, $doorStateWriter);
