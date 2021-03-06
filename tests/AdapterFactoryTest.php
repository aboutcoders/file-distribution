<?php
namespace Abc\Filesystem\Tests;

use Abc\Filesystem\AdapterFactory;
use Abc\Filesystem\FilesystemType;

class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var AdapterFactory */
    protected $subject;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new AdapterFactory();
    }

    /**
     * @dataProvider getValidArgs()
     */
    public function testCreate($type, $path, $options, $expectedAdapterClass)
    {
        $filesystem = $this->subject->create($type, $path, $options);

        $this->assertInstanceOf('Gaufrette\Adapter', $filesystem);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateAdapterThrowsInvalidArgumentExceptionForUnsupportedTypes()
    {
        $this->subject->create('foobar', '/path/to/nowhere');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateThrowsInvalidArgumentExceptionIfFtpHostIsEmpty()
    {
        $filesystem = $this->subject->create(FilesystemType::FTP, '/foobar');
    }

    /**
     * @return array
     */
    public static function getValidArgs()
    {
        return array(
            array(FilesystemType::LOCAL, '/tmp', array(), 'Gaufrette\Adapter\Local'),
            array(FilesystemType::FTP, '/tmp', array('host' => 'ftp.domain.tld'), 'Gaufrette\Adapter\Ftp')
        );
    }
}