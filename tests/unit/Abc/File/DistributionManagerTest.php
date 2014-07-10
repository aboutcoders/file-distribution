<?php
namespace Abc\File;


use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\Exception\FileNotFound;

class DistributionManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $baseTestUrl = '/tmp/';
    protected $testFile    = 'test1.txt';
    protected $sourceFiles;
    protected $filesystemFactory;
    /** @var DistributionManager */
    protected $subject;
    protected $filesystem;

    protected function setUp()
    {
        parent::setUp();
        $this->filesystemFactory = $this->getMockBuilder('Abc\File\FilesystemFactory')->disableOriginalConstructor()->getMock();
        $this->filesystem        = $this->getFilesystemExpectations($this->getFileExpectations());
        $this->sourceFiles       = __DIR__ . '/../../../fixtures/files/';
    }


    public function testDistributeWithValidDataDistributesFile()
    {
        $destinationFilesystem = $this->getMockBuilder('Gaufrette\Filesystem')->disableOriginalConstructor()->getMock();
        $this->filesystemFactory->expects($this->at(0))
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));
        $this->filesystemFactory->expects($this->at(1))
            ->method('buildFilesystem')
            ->will($this->returnValue($destinationFilesystem));

        $this->subject = new DistributionManager($this->filesystemFactory);

        $sourceLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->sourceFiles);
        $file           = new File('test1.txt', $this->testFile, 14, $sourceLocation);
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);

        $output = $this->subject->distribute($file, $targetLocation);
        $this->assertInstanceOf('Abc\File\FileInterface', $output, 'Result type is not as expected');
    }

    public function testCopyFileWithValidDataDistributesFile()
    {
        $this->filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));
        $this->subject  = new DistributionManager($this->filesystemFactory);
        $sourceLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->sourceFiles);
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);
        $sourceFile     = new File('test1.txt', $this->testFile, 14, $sourceLocation);
        $targetFile     = new File('test1.txt', $this->testFile, 14, $targetLocation);

        $this->subject->copyFile($sourceFile, $targetFile);
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testCopyFileWithExistingFileThrowsException()
    {
        $filesystemFactory = $this->getMockBuilder('Abc\File\FilesystemFactory')->disableOriginalConstructor()->getMock();
        $sourceLocation    = $this->getLocationExpectations(FilesystemType::Filesystem, $this->sourceFiles);
        $targetLocation    = $this->getLocationExpectations(FilesystemType::Filesystem, $this->sourceFiles);
        $sourceFile        = new File('test1.txt', $this->testFile, 14, $sourceLocation);
        $targetFile        = new File('test1.txt', $this->testFile, 14, $targetLocation);
        $sourceFilesystem  = $this->getMockBuilder('Gaufrette\Filesystem')->disableOriginalConstructor()->getMock();

        $sourceFilesystem->expects($this->any())
            ->method('write')
            ->will($this->throwException(new FileAlreadyExists($this->testFile)));
        $filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($sourceFilesystem));

        $subject = new DistributionManager($filesystemFactory);
        $subject->copyFile($sourceFile, $targetFile);
    }

    public function testCreateFileWithValidDataCreatesFile()
    {
        $this->filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));

        $this->subject  = new DistributionManager($this->filesystemFactory);
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);
        $result         = $this->subject->createFile($targetLocation);
        $this->assertInstanceOf('Abc\File\File', $result);
    }

    public function testDeleteFileWithValidFile()
    {
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);
        $file           = new File('testFileToDelete.txt', '/delete/', 1, $targetLocation);

        $this->filesystem->expects($this->any())
            ->method('delete')
            ->will($this->returnValue(true));

        $this->filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));

        $this->subject = new DistributionManager($this->filesystemFactory);

        $result = $this->subject->delete($file);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Gaufrette\Exception\FileNotFound
     */
    public function testDeleteFileWithNonExistingFileThrowsException()
    {
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);
        $file           = new File('testFileToDelete.txt', '/delete/', 1, $targetLocation);

        $this->filesystem->expects($this->any())
            ->method('delete')
            ->will($this->throwException(new FileNotFound($file->getPath())));

        $this->filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));

        $this->subject = new DistributionManager($this->filesystemFactory);

        $this->subject->delete($file);
    }

    public function testExistsWithExistingFileReturnsTrue()
    {
        $targetLocation = $this->getLocationExpectations(FilesystemType::Filesystem, $this->baseTestUrl);
        $file           = new File('testFileToDelete.txt', '/delete/', 1, $targetLocation);

        $this->filesystem->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));

        $this->filesystemFactory->expects($this->any())
            ->method('buildFilesystem')
            ->will($this->returnValue($this->filesystem));

        $this->subject = new DistributionManager($this->filesystemFactory);

        $result = $this->subject->exists($file);
        $this->assertTrue($result);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFileExpectations()
    {
        $file = $this->getMockBuilder('Gaufrette\File')->disableOriginalConstructor()->getMock();

        $file->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($this->testFile));
        return $file;
    }

    /**
     * @param $file
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFilesystemExpectations($file)
    {
        $sourceFilesystem = $this->getMockBuilder('Gaufrette\Filesystem')->disableOriginalConstructor()->getMock();
        $sourceFilesystem->expects($this->any())
            ->method('write')
            ->will($this->returnValue(1));
        $sourceFilesystem->expects($this->any())
            ->method('createFile')
            ->will($this->returnValue($file));
        return $sourceFilesystem;
    }
}
 