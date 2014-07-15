<?php

namespace Abc\Filesystem;

use Gaufrette\Adapter\Local;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class LocalFilesystemTest extends \PHPUnit_Framework_TestCase
{
    /** @var Filesystem */
    protected $subject;
    /** @var string */
    protected $path;
    /** @var string */
    private $fixtureDir;

    public function setUp()
    {
        $this->fixtureDir = dirname(__FILE__) . '/../fixtures/test-directory';
        $this->path       = dirname(__FILE__) . '/../../build/unit/';

        if(is_dir($this->path))
        {
            $filesystem = new LocalFilesystem;
            $filesystem->remove($this->path);
        }


        mkdir($this->path);

        $this->subject = $this->createFilesystem($this->path);
    }

    public function tearDown()
    {
        if(is_dir($this->path))
        {
            $filesystem = new LocalFilesystem;
            $filesystem->remove($this->path);
        }
    }

    public function testCreateClientCreatesPathIfCreateIsTrue()
    {
        $filesystem = $this->subject->createFilesystem('foo/bar', true);

        $this->assertInstanceOf('Abc\Filesystem\Filesystem', $filesystem);
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateClientThrowsInvalidArgumentException()
    {
        $this->subject->createFilesystem('foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddThrowsInvalidArgumentException()
    {
        $this->subject->upload('/path/to/nowhere');
    }

    public function testAddWithFile()
    {
        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->subject->upload($path);

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }

    public function testAddWithFileAndOverwrite()
    {
        $filename = 'foobar.txt';

        $localFilesystem = new LocalFilesystem();
        $localFilesystem->touch($this->path . '/' . $filename);

        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->subject->upload($path, null, true);

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testAddWithFileThrowsExceptionIfFileExists()
    {
        $filename = 'foobar.txt';

        $localFilesystem = new LocalFilesystem();
        $localFilesystem->touch($this->path . '/' . $filename);

        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . $filename;

        $this->subject->upload($path);

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }


    public function testAddWithDirectory()
    {
        $path = $this->fixtureDir;

        $this->subject->upload($path);

        $this->assertTrue(is_dir($this->path . '/test-directory'));
        $this->assertTrue(is_dir($this->path . '/test-directory/barfoo'));
        $this->assertTrue(is_dir($this->path . '/test-directory/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar.txt'));
    }

    public function testAddWithDirectoryAndTargetPath()
    {
        $path = $this->fixtureDir;

        $this->subject->upload($path, 'foobar');

        $this->assertTrue(is_dir($this->path . '/foobar/test-directory'));
        $this->assertTrue(is_dir($this->path . '/foobar/test-directory/barfoo'));
        $this->assertTrue(is_dir($this->path . '/foobar/test-directory/foobar'));
        $this->assertTrue(file_exists($this->path . '/foobar/test-directory/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/foobar/test-directory/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/foobar/test-directory/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/foobar/test-directory/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/foobar/test-directory/foobar/foobar.txt'));
    }


    public function testDownloadWithFile()
    {
        $subject = $this->createFilesystem($this->fixtureDir);

        $subject->download('/foobar.txt', $this->path);

        $this->assertTrue(file_exists($this->path . '/foobar.txt'));
        $this->assertSame(file_get_contents($this->fixtureDir . '/foobar.txt'), file_get_contents($this->path . '/foobar.txt'));
    }


    public function testDownloadWithDirectoryAndWithRemotePath()
    {
        $subject = $this->createFilesystem($this->fixtureDir . '/..');

        $subject->download('test-directory', $this->path);

        $this->assertTrue(is_dir($this->path . '/test-directory'));
        $this->assertTrue(is_dir($this->path . '/test-directory/barfoo'));
        $this->assertTrue(is_dir($this->path . '/test-directory/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar.txt'));
    }

    /**
     * @dataProvider getEmptyRemotePath
     */
    public function testDownloadWithDirectoryAndWithoutRemotePath($path)
    {
        $subject = $this->createFilesystem($this->fixtureDir);

        $subject->download($path, $this->path);

        $this->assertTrue(is_dir($this->path . '/barfoo'));
        $this->assertTrue(is_dir($this->path . '/foobar'));
        $this->assertTrue(file_exists($this->path . '/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/foobar/foobar.txt'));
    }

    /**
     * @expectedException \Gaufrette\Exception\FileNotFound
     */
    public function testDownloadThrowsFileNotFoundException()
    {
        $subject = $this->createFilesystem($this->fixtureDir);

        $subject->download('unknown', $this->path);
    }

    public static function getEmptyRemotePath()
    {
        return array(
            array(''),
            array('/')
        );
    }

    private function createFilesystem($path)
    {
        return new Filesystem(new AdapterFactory(), $this->createDefinition(FilesystemType::LOCAL, $path));
    }


    private function createDefinition($type, $path, array $options = array())
    {
        $definition = new Definition();
        $definition->setType($type);
        $definition->setPath($path);
        $definition->setProperties($options);

        return $definition;
    }
}