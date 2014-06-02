<?php
namespace Abc\File;


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
        $file = new File($this->testFile, $this->testFile, 14, $this->sourceLocation);

        $output = $this->subject->distribute($file, $this->destinationLocation);
        $this->assertInstanceOf('Abc\File\FileInterface', $output, 'Transferred file type is not as expected');
        $this->assertEquals(14, $output->getFileSize(), 'Transferred file size is not as expected');
        $this->assertEquals($this->testFile, $output->getPath(), 'Transferred file path is not as expected');
    }

    public function test_CopyFileWithValidFileDistributesFile()
    {
        $file       = new File($this->testFile, $this->testFile, 14, $this->sourceLocation);
        $targetFile = new File($this->testFile, $this->testFile, 14, $this->destinationLocation);
        $result     = $this->subject->copyFile($file, $targetFile);
        $this->assertGreaterThan(0, $result);
    }

    public function testCreateFileCreatesEmptyFile()
    {
        $result = $this->subject->createFile($this->destinationLocation);
        $this->assertInstanceOf('Abc\File\FileInterface', $result, 'Transferred file type is not as expected');
        $this->assertEquals(0, $result->getFileSize(), 'Transferred file size is not as expected');
        $this->assertNotEmpty($result->getPath(), 'Transferred file path is not as expected');
    }
}