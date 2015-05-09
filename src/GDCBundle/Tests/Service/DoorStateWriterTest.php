<?php

namespace GDCBundle\Tests\Service;

use GDC\Door\State;
use GDCBundle\Event\AutoSequenceStartedEvent;
use GDCBundle\Model\AutoSequenceName;
use GDCBundle\Model\Microtime;
use GDCBundle\Service\AutoSequence\CloseAfterOneTransit;
use GDCBundle\Service\AutoSequence\TriggerDoor;
use GDCBundle\Service\DoorStateWriter;
use org\bovigo\vfs\vfsStream;

class DoorStateWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoorStateWriter
     */
    private $SUT;

    /**
     * @var \SplFileInfo
     */
    private $filePath;

    protected function setUp()
    {
        vfsStream::setup('path/to');
        $this->filePath = new \SplFileInfo(vfsStream::url('path/to/state.json'));
        $this->SUT = new DoorStateWriter($this->filePath);
    }

    /**
     * @test
     */
    public function it_should_write_a_json_file_to_a_given_path()
    {
        $this->assertFileNotExists((string)($this->filePath));
        $this->SUT->write(State::CLOSED(), new Microtime());
        $this->assertFileExists((string)($this->filePath));
        $this->assertJson(file_get_contents($this->filePath));
    }

    /**
     * @test
     */
    public function it_should_write_the_door_state_into_the_json()
    {
        $this->SUT->write(State::CLOSED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(State::CLOSED()->getValue(), $json['doorState']);

        $this->SUT->write(State::OPENED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(State::OPENED()->getValue(), $json['doorState']);
    }

    /**
     * @test
     */
    public function it_should_write_the_current_date_into_the_json()
    {
        $time = new Microtime('0.23675900 1430479339');
        $nowAsIsoString = '2015-05-01T13:22:19+0200';
        $this->SUT->write(State::CLOSED(), $time);
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals($nowAsIsoString, $json['date']);

        $time = new Microtime('0.43932300 1430479381');
        $nowAsIsoString = '2015-05-01T13:23:01+0200';
        $this->SUT->write(State::CLOSED(), $time);
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals($nowAsIsoString, $json['date']);
    }

    /**
     * @test
     */
    public function it_should_write_an_auto_sequence_element_into_the_json()
    {
        $this->SUT->write(State::CLOSED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(null, $json['autoSequence']);
    }

    /**
     * @test
     */
    public function it_should_write_a_newly_started_auto_sequence()
    {
        $this->SUT->onAutoSequenceStarted(new AutoSequenceStartedEvent(new AutoSequenceName(CloseAfterOneTransit::NAME)));

        $this->SUT->write(State::CLOSED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(CloseAfterOneTransit::NAME, $json['autoSequence']);
    }

    /**
     * @test
     */
    public function it_should_clean_out_auto_sequence_on_termination()
    {
        $this->SUT->onAutoSequenceStarted(new AutoSequenceStartedEvent(new AutoSequenceName(CloseAfterOneTransit::NAME)));

        $this->SUT->write(State::CLOSED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(CloseAfterOneTransit::NAME, $json['autoSequence']);

        $this->SUT->onAutoSequenceTerminated();

        $this->SUT->write(State::CLOSED(), new Microtime());
        $json = json_decode(file_get_contents($this->filePath), true);
        $this->assertEquals(null, $json['autoSequence']);
    }
}
