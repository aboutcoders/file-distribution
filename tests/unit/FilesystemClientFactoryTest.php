<?php
namespace Abc\File;

class FilesystemClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FilesystemClientFactory */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new FilesystemClientFactory();
    }



    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateAdapterThrowsInvalidArgumentExceptionForUnsupportedTypes()
    {
        $this->subject->createAdapter('foobar', '/path/to/nowhere');
    }

    /**
     * @dataProvider getValidArgs()
     */
    public function testCreateAdapter($type, $path, $options, $expectedAdapterClass)
    {
        $filesystem = $this->subject->createAdapter($type, $path, $options);

        $this->assertInstanceOf('Gaufrette\Adapter', $filesystem);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateAdapterThrowsInvalidArgumentExceptionIfFtpHostIsEmpty()
    {
        $filesystem = $this->subject->createAdapter(FilesystemType::FTP, '/foobar');
    }

    /**
     * @dataProvider getValidArgs()
     */
    public function testCreate($type, $path, $options, $expectedAdapterClass)
    {
        $filesystem = new Filesystem();
        $filesystem->setType($type);
        $filesystem->setPath($path);
        $filesystem->setProperties($options);

        $client = $this->subject->create($filesystem);
        $this->assertInstanceOf('Abc\File\FilesystemClient', $client);

    }


    public static function getValidArgs()
    {
        return array(
            array(FilesystemType::LOCAL, '/tmp', array(), 'Gaufrette\Adapter\Local'),
            array(FilesystemType::FTP, '/tmp', array('host' => 'ftp.domain.tld'), 'Gaufrette\Adapter\Ftp')
        );
    }
}
 