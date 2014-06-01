<?php
namespace Abc\File\Client;


class FileDistributionClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileDistributionClient */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new FileDistributionClient();
    }

    public function testDistributeWithValidDataDistributesFile()
    {
        $file = $this->getFileExpectations();

        $filesystem = $this->getMockBuilder('Gaufrette\Filesystem')->disableOriginalConstructor()->getMock();
        $filesystem->expects($this->any())
            ->method('write')
            ->with($this->equalTo('testFile.txt'), $this->equalTo('File contents'))
            ->will($this->returnValue(1));

        $location = $this->getLocationExpectations($filesystem);

        $output = $this->subject->distribute($file, $location);
        $this->assertGreaterThan(0, $output, 'Transferred file size is not as expected');
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testDistributeWithInvalidKeyThrowsException()
    {
        $file = $this->getFileExpectations();

        $filesystem = $this->getMockBuilder('Gaufrette\Filesystem')->disableOriginalConstructor()->getMock();
        $filesystem->expects($this->any())
            ->method('write')
            ->with($this->equalTo('testFile.txt'), $this->equalTo('File contents'))
            ->will($this->throwException(new \RuntimeException));

        $location = $this->getLocationExpectations($filesystem);

        $output = $this->subject->distribute($file, $location);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileExpectations()
    {
        $file = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();
        $file->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('testFile.txt'));
        $file->expects($this->any())
            ->method('getContent')
            ->will($this->returnValue('File contents'));
        return $file;
    }

    /**
     * @param $filesystem
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getLocationExpectations($filesystem)
    {
        $location = $this->getMock('Abc\File\LocationInterface');
        $location->expects($this->any())
            ->method('getFilesystem')
            ->will($this->returnValue($filesystem));
        return $location;
    }
}
 