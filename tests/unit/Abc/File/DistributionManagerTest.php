<?php
namespace Abc\File;


class DistributionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $baseTestUrl = '/tmp/';
    protected $testFile    = 'test1.txt';
    protected $sourceFiles;

    /** @var DistributionManager */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject     = new DistributionManager();
        $this->sourceFiles = __DIR__ . '/../../../fixtures/files/';
    }

    protected function tearDown()
    {
        parent::tearDown();
        @unlink($this->baseTestUrl . $this->testFile);
    }

    public function testDistributeWithValidDataDistributesFile()
    {
        $sourceLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->sourceFiles);
        $file           = new File('test1.txt', $this->testFile, 14, $sourceLocation);
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);

        $output = $this->subject->distribute($file, $targetLocation);
        $this->assertInstanceOf('Abc\File\FileInterface', $output, 'Result type is not as expected');
    }


    /**
     * @param string $type
     * @param string $url
     * @param array  $properties
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getLocationExpectations($type, $url, $properties = array())
    {
        $location = $this->getMock('Abc\File\LocationInterface');
        $location->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $location->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValue($url));
        $location->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        return $location;
    }
}
 