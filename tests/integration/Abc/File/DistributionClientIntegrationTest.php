<?php
namespace Abc\File;


use Abc\File\LocationInterface;
use Gaufrette\File;

abstract class DistributionClientIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $baseTestUrl = '/tmp/';
    protected $testFile    = 'test1.txt';
    /** @var string */
    protected $sourceFiles;
    /** @var DistributionManager */
    protected $subject;
    /** @var LocationInterface */
    protected $destinationLocation;
    /** @var LocationInterface */
    protected $sourceLocation;

    public function testDistributeWithValidFileDistributesFile()
    {
        $file = new File($this->testFile, $this->sourceLocation->getFileSystem());

        $output = $this->subject->distribute($file, $this->destinationLocation);
        $this->assertEquals(14, $output, 'Transferred file size is not as expected');
    }

    public function testDistributeWithValidDataOverridesFile()
    {
        $this->destinationLocation->getFileSystem()->write($this->testFile, 'Test');
        $file = new File($this->testFile, $this->sourceLocation->getFileSystem());

        $output = $this->subject->distribute($file, $this->destinationLocation, true);
        $this->assertEquals(14, $output, 'Transferred file size is not as expected');
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testDistributeWithExistingFileThrowsException()
    {
        $this->destinationLocation->getFileSystem()->write($this->testFile, 'Test');
        $file = new File($this->testFile, $this->sourceLocation->getFileSystem());

        $this->subject->distribute($file, $this->destinationLocation);
    }

    /**
     * @expectedException \Gaufrette\Exception\FileNotFound
     */
    public function testDistributeWithNonExistingFileThrowsException()
    {
        $file = new File($this->testFile . 'a', $this->sourceLocation->getFileSystem());

        $this->subject->distribute($file, $this->destinationLocation);
    }

} 