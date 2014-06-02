<?php
namespace Abc\File;


use Abc\File\Location\FilesystemLocation;
use Abc\File\Location\FtpLocation;

class FTPDistributionClientIntegrationTest extends DistributionClientIntegrationTest
{

    protected $host;
    protected $options;

    protected function setUp()
    {
        parent::setUp();
        $this->subject             = new DistributionManager();
        $this->sourceFiles         = __DIR__ . '/../../../../fixtures/files/';
        $this->sourceLocation      = new FilesystemLocation($this->sourceFiles);
        $this->destinationLocation = new FtpLocation($this->baseTestUrl, $this->host, $this->options);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->destinationLocation->getFileSystem()->delete($this->testFile);
    }


}
 