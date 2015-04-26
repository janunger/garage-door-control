<?php

use Doctrine\Common\Collections\ArrayCollection;
use GDC\Sensor\Role;
use GDCBundle\Entity\SensorLogEntry;
use Pkj\Raspberry\PiFace\Emulator\Components\InputPin;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

class SensorLoggerTest extends EndToEndTestCase
{
    private static $rootDir;

    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \GDCBundle\Entity\SensorLogEntryRepository
     */
    private $sensorLogEntryRepository;

    /**
     * @var InputPin
     */
    private $inputPinDoorClosed;

    /**
     * @var InputPin
     */
    private $inputPinDoorOpened;

    /**
     * @var InputPin
     */
    private $inputPinPhotoInterrupter;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$rootDir = realpath(__DIR__ . '/../..');
        require_once self::$rootDir . '/app/AppKernel.php';

        self::resetDatabase();
    }

    protected static function resetDatabase()
    {
        self::runConsoleCommand('doctrine:schema:drop --force');
        self::runConsoleCommand('doctrine:schema:create');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->kernel = new AppKernel('dev', true);
        $this->kernel->boot();
        $this->container                = $this->kernel->getContainer();
        $this->sensorLogEntryRepository = $this->container->get('gdc.sensor_log_entry_repository');
        $this->inputPinDoorClosed       = $this->container->get('gdc.piface_input_pin.door_closed');
        $this->inputPinDoorOpened       = $this->container->get('gdc.piface_input_pin.door_opened');
        $this->inputPinPhotoInterrupter = $this->container->get('gdc.piface_input_pin.photo_interrupter');
    }

    /**
     * @test
     */
    public function it_should_log_all_sensors_at_startup_of_event_loop()
    {
        $this->resetEmulator();

        $entries = $this->sensorLogEntryRepository->findAll();
        $this->assertCount(0, $entries);

        $expectedEntryCount = 3;
        self::$eventLoop->start();

        $entries = $this->expectEntries($expectedEntryCount);

        $this->assertCount($expectedEntryCount, $entries);
        $this->assertEquals(Role::DOOR_CLOSED(), $entries[0]->getRole());
        $this->assertTrue($entries[0]->isOn());
        $this->assertEquals(Role::DOOR_OPENED(), $entries[1]->getRole());
        $this->assertFalse($entries[1]->isOn());
        $this->assertEquals(Role::PHOTO_INTERRUPTER(), $entries[2]->getRole());
        $this->assertTrue($entries[2]->isOn());
    }

    /**
     * @test
     */
    public function it_should_log_a_changed_sensor_state()
    {
        $this->inputPinDoorClosed->setIsOn(false);

        $expectedEntryCount = 4;
        $entries            = $this->expectEntries($expectedEntryCount);

        $this->assertCount($expectedEntryCount, $entries);
        $this->assertEquals(Role::DOOR_CLOSED(), $entries[3]->getRole());
        $this->assertFalse($entries[3]->isOn());


        $this->inputPinDoorOpened->setIsOn(true);

        $expectedEntryCount = 5;
        $entries            = $this->expectEntries($expectedEntryCount);

        $this->assertCount($expectedEntryCount, $entries);
        $this->assertEquals(Role::DOOR_OPENED(), $entries[4]->getRole());
        $this->assertTrue($entries[4]->isOn());
    }

    protected static function runConsoleCommand($command)
    {
        $process = new Process(self::$rootDir . '/app/console ' . $command);
        $process->run();
        if (!$process->isSuccessful()) {
            self::fail($process->getErrorOutput());
        }
    }

    /**
     * @param int $expectedEntryCount
     * @param int $timeout
     * @return ArrayCollection|SensorLogEntry[]
     */
    private function expectEntries($expectedEntryCount, $timeout = 5)
    {
        $start = time();
        do {
            $entries = $this->sensorLogEntryRepository->findAll();
            if (count($entries) >= $expectedEntryCount) {
                break;
            }
            if (time() > $start + $timeout) {
                $this->fail('Log did not get expected entry count.');
            }
            usleep(300000);
        } while (true);

        return $entries;
    }

    private function resetEmulator()
    {
        $this->inputPinDoorClosed->setIsOn(true);
        $this->inputPinDoorOpened->setIsOn(false);
        $this->inputPinPhotoInterrupter->setIsOn(true);
    }
}
