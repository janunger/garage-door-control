<?php

namespace GDCBundle\Tests\Service\SensorLogger;

use Carbon\Carbon;
use GDC\Sensor\Role;
use GDCBundle\Entity\SensorLogEntry;
use GDCBundle\Entity\SensorLogEntryRepository;
use GDCBundle\Service\SensorLogger\SensorLogger;
use GDCBundle\Service\SensorLogger\StateWatcher;
use GDCBundle\Tests\AbstractTestCase;

class SensorLoggerTest extends AbstractTestCase
{
    /**
     * @var SensorLogger
     */
    protected $SUT;

    /**
     * @var $repo SensorLogEntryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logEntryRepository;

    /**
     * @var InputPinMock
     */
    protected $sensorDoorClosed;

    /**
     * @var InputPinMock
     */
    protected $sensorDoorOpened;

    /**
     * @var InputPinMock
     */
    protected $sensorPhotoInterrupter;

    protected function setUp()
    {
        Carbon::setTestNow(Carbon::createFromDate());

        $this->logEntryRepository = $this->createMock('\GDCBundle\Entity\SensorLogEntryRepository');

        $this->sensorDoorClosed = new InputPinMock();
        $this->sensorDoorClosed->setIsOn(true);

        $this->sensorDoorOpened = new InputPinMock();
        $this->sensorDoorOpened->setIsOn(false);

        $this->sensorPhotoInterrupter = new InputPinMock();
        $this->sensorPhotoInterrupter->setIsOn(true);

        $this->SUT = new SensorLogger(
            $this->logEntryRepository,
            [
                new StateWatcher($this->sensorDoorClosed, Role::DOOR_CLOSED()),
                new StateWatcher($this->sensorDoorOpened, Role::DOOR_OPENED()),
                new StateWatcher($this->sensorPhotoInterrupter, Role::PHOTO_INTERRUPTER()),
            ]
        );
    }

    /**
     * @test
     */
    public function it_should_log_the_initial_state_after_being_instantiated_with_sensors()
    {
        $this->logEntryRepository->expects($this->exactly(3))->method('save');
        $this->logEntryRepository->expects($this->at(0))->method('save')->with(
            new SensorLogEntry(Role::DOOR_CLOSED(), true, Carbon::now())
        );
        $this->logEntryRepository->expects($this->at(1))->method('save')->with(
            new SensorLogEntry(Role::DOOR_OPENED(), false, Carbon::now())
        );
        $this->logEntryRepository->expects($this->at(2))->method('save')->with(
            new SensorLogEntry(Role::PHOTO_INTERRUPTER(), true, Carbon::now())
        );

        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_not_log_if_state_does_not_change_between_two_cycles()
    {
        $this->SUT->execute();

        $this->logEntryRepository->expects($this->never())->method('save');

        $this->SUT->execute();
    }

    /**
     * @test
     */
    public function it_should_log_only_changes_state()
    {
        $this->SUT->execute();

        $this->sensorDoorClosed->setIsOn(false);
        $this->logEntryRepository->expects($this->once())->method('save')->with(
            new SensorLogEntry(Role::DOOR_CLOSED(), false, Carbon::now())
        );

        $this->SUT->execute();
    }
}