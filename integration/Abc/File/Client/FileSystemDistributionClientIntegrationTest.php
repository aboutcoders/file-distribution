<?php
namespace Abc\File\Client;


use Abc\File\Location\FilesystemLocation;
use Gaufrette\File;

class FileSystemDistributionClientIntegrationTest extends \PHPUnit_Framework_TestCase
{
    protected $baseTestUrl = '/tmp/';
    protected $testFile    = 'test1.txt';
    /** @var FileDistributionClient */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new FileDistributionClient();
    }

    protected function tearDown()
    {
        parent::tearDown();
        @unlink($this->baseTestUrl . $this->testFile);
    }


    public function testDistributeWithValidFileDistributesFile()
    {
        $filesystemLocation = new FilesystemLocation(__DIR__ . '/../../../fixtures/files/');
        $file               = new File($this->testFile, $filesystemLocation->getFileSystem());

        $destinationLocation = new FilesystemLocation($this->baseTestUrl);

        $output = $this->subject->distribute($file, $destinationLocation);
        $this->assertEquals(14, $output, 'Transferred file size is not as expected');
    }

    public function testDistributeWithValidDataOverridesFile()
    {
        touch($this->baseTestUrl . $this->testFile);
        $filesystemLocation = new FilesystemLocation(__DIR__ . '/../../../fixtures/files/');
        $file               = new File($this->testFile, $filesystemLocation->getFileSystem());

        $destinationLocation = new FilesystemLocation($this->baseTestUrl);

        $output = $this->subject->distribute($file, $destinationLocation, true);
        $this->assertEquals(14, $output, 'Transferred file size is not as expected');
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testDistributeWithExistingFileThrowsException()
    {
        touch($this->baseTestUrl . $this->testFile);
        $filesystemLocation = new FilesystemLocation(__DIR__ . '/../../../fixtures/files/');
        $file               = new File($this->testFile, $filesystemLocation->getFileSystem());

        $destinationLocation = new FilesystemLocation($this->baseTestUrl);

        $this->subject->distribute($file, $destinationLocation);
    }

    /**
     * @expectedException \Gaufrette\Exception\FileNotFound
     */
    public function testDistributeWithNonExistingFileThrowsException()
    {
        $filesystemLocation = new FilesystemLocation(__DIR__ . '/../../../fixtures/files/');
        $file               = new File($this->testFile . 'a', $filesystemLocation->getFileSystem());

        $destinationLocation = new FilesystemLocation($this->baseTestUrl);

        $this->subject->distribute($file, $destinationLocation);
    }

}
 