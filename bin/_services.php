<?php

declare(strict_types=1);

$config = require __DIR__ . '/../etc/config.php';

if ($config['use_hardware']) {
    $piFace                  = new \JUIT\PiFace\Hardware\PiFace();
    $actorMotor              = new \JUIT\PiFace\Hardware\OutputPin($config['pin_id_motor_trigger']);
} else {
    $emulatorDataFile = new SplFileInfo(__DIR__ . '/../var/emulator/emulator');
    $piFace           = new \JUIT\PiFace\Emulator\PiFace($emulatorDataFile);
    $actorMotor       = new \JUIT\PiFace\Emulator\OutputPin();
}

$door = new \JUIT\GDC\Door\Door(
    $piFace,
    new \JUIT\GDC\Model\InputPinIdDoorClosed($config['pin_id_closed']),
    new \JUIT\GDC\Model\InputPinIdDoorOpened($config['pin_id_opened']),
    $actorMotor
);

$transport = new Swift_SmtpTransport($config['mailer_host'], $config['mailer_port']);
if (null !== $config['mailer_encryption']) {
    $transport->setEncryption($config['mailer_encryption']);
}
if (null !== $config['mailer_username']) {
    $transport->setUsername($config['mailer_username']);
}
if (null !== $config['mailer_password']) {
    $transport->setPassword($config['mailer_password']);
}
if (null !== $config['mailer_auth_mode']) {
    $transport->setAuthMode($config['mailer_auth_mode']);
}

$swiftMailer      = new Swift_Mailer($transport);
$senderAddress    = $config['mailer_sender_address'];
$senderName       = $config['mailer_sender_name'];
$recipientAddress = $config['mailer_recipient_address'];
$recipientName    = $config['mailer_recipient_name'];
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
