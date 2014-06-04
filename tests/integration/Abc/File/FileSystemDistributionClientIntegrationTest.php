<?php
namespace Abc\File;

class FileSystemDistributionClientIntegrationTest extends DistributionClientIntegrationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->subject        = new DistributionManager(new FilesystemFactory());
        $this->sourceFiles    = __DIR__ . '/../../../fixtures/files/';
        $this->sourceLocation = new Location();
        $this->sourceLocation->setType(FilesystemType::Filesystem);
        $this->sourceLocation->setPath($this->sourceFiles);
        $this->destinationLocation = new Location();
        $this->destinationLocation->setType(FilesystemType::Filesystem);
        $this->destinationLocation->setPath($this->baseTestUrl);
    }

    protected function tearDown()
    {
        parent::tearDown();
        @unlink($this->baseTestUrl . $this->testFile);
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testDistributeWithExistingFileThrowsException()
    {
        touch($this->baseTestUrl . $this->testFile);
        $file = new File($this->testFile, $this->testFile, 14, $this->sourceLocation);
        $this->subject->distribute($file, $this->destinationLocation);
    }

    public function testCopyFileWithValidFileDistributesFile()
    {
        touch($this->baseTestUrl . $this->testFile);
        $file       = new File($this->testFile, $this->testFile, 14, $this->sourceLocation);
        $targetFile = new File($this->testFile, $this->testFile, 14, $this->destinationLocation);
        $result     = $this->subject->copyFile($file, $targetFile, true);
        $this->assertGreaterThan(0, $result);
    }
}
 