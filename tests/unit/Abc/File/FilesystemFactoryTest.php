<?php
namespace Abc\File;


use Abc\File\Exception\FilesystemException;

class FilesystemFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemFactory */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new FilesystemFactory();

    }

    /**
     * @param $location
     * @dataProvider getFilesystems
     */
    public function testBuildFilesystemReturnsLocalFilesystem($location)
    {
        $filesystem = $this->subject->buildFilesystem($location);
        $this->assertInstanceOf('Gaufrette\Filesystem', $filesystem, 'Filesystem type is not as expected');
    }

    /**
     * @expectedException \Abc\File\Exception\FilesystemException
     * @expectedExceptionMessage Host is not set for FTP adapter
     */
    public function testBuildFilesystemFTPWithEmptyHostThrowsException()
    {
        $location = $this->getLocationExpectations(FilesystemType::FTP, '/');
        $this->subject->buildFilesystem($location);
    }

    /**
     * @expectedException \Abc\File\Exception\FilesystemException
     * @expectedExceptionMessage Adapter abc does not exist
     */
    public function testBuildFilesystemWithNonExistingAdapterThrowsException()
    {
        $location = $this->getLocationExpectations('abc', '/');
        $this->subject->buildFilesystem($location);
    }

    public function getFilesystems()
    {
        return array(
            array($this->getLocationExpectations(FilesystemType::Filesystem, '/')),
            array($this->getLocationExpectations(FilesystemType::FTP, '/', array('host' => 'localhost'))),
        );
    }

    /**
     * @param string $type
     * @param string $url
     * @param array  $properties
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getLocationExpectations($type, $url, $properties = array())
    {
        $location = $this->getMock('Abc\File\FilesystemInterface');
        $location->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($type));
        $location->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($url));
        $location->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($properties));
        return $location;
    }
}
 