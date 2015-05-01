<?php

namespace GDC\Tests\WatchDog;

use GDC\Door\DoorInterface;
use GDC\Tests\AbstractTestCase;
use GDC\WatchDog\Messenger;
use GDC\WatchDog\WatchDog;
use GDCBundle\Entity\DoorStateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class WatchDogTestCase extends AbstractTestCase
{
    /**
     * @var DoorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $door;

    /**
     * @var Messenger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messenger;

    /**
     * @var DoorStateRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $doorStateRepository;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDispatcher;

    protected function setUp()
    {
        $this->door                = $this->createMock('GDC\Door\DoorInterface');
        $this->messenger           = $this->createMock('GDC\WatchDog\Messenger');
        $this->doorStateRepository = $this->createMock('GDCBundle\Entity\DoorStateRepository');
        $this->eventDispatcher     = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }

    /**
     * @return WatchDog
     */
    protected function createSUTInstance()
    {
        return new WatchDog($this->door, $this->messenger, $this->doorStateRepository, $this->eventDispatcher);
    }
}