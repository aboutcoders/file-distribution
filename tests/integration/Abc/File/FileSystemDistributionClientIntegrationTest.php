<?php
namespace Abc\File;


use Abc\File\Location\FilesystemLocation;

class FileSystemDistributionClientIntegrationTest extends DistributionClientIntegrationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->subject             = new DistributionManager();
        $this->sourceFiles         = __DIR__ . '/../../../../fixtures/files/';
        $this->sourceLocation      = new FilesystemLocation($this->sourceFiles);
        $this->destinationLocation = new FilesystemLocation($this->baseTestUrl);
    }

    protected function tearDown()
    {
        parent::tearDown();
        @unlink($this->baseTestUrl . $this->testFile);
    }

}
 