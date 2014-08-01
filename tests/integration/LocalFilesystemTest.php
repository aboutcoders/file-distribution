<?php

namespace Abc\Filesystem;

use Gaufrette\Adapter\Local;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
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
        $this->path       = dirname(__FILE__) . '/../../build/unit/filesystem';

        $filesystem = new LocalFilesystem;

        if(is_dir($this->path))
        {
            $filesystem->remove($this->path);
        }

        $filesystem->mkdir($this->path);


        $this->subject = $this->createFilesystem($this->path);
    }

    public function tearDown()
    {
        /*if(is_dir($this->path))
        {
            $filesystem = new LocalFilesystem;
            $filesystem->remove($this->path);
        }*/
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
    public function testUploadThrowsInvalidArgumentException()
    {
        $this->subject->upload('/path/to/nowhere', 'nowhere');
    }

    public function testUploadWithFile()
    {
        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->subject->upload($path, basename($path));

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }

    public function testUploadWithFileAndOverwrite()
    {
        $filename = 'foobar.txt';

        $localFilesystem = new LocalFilesystem();
        $localFilesystem->touch($this->path . '/' . $filename);

        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->subject->upload($path, basename($path), true);

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }

    /**
     * @expectedException \Gaufrette\Exception\FileAlreadyExists
     */
    public function testUploadWithFileThrowsExceptionIfFileExists()
    {
        $filename = 'foobar.txt';

        $localFilesystem = new LocalFilesystem();
        $localFilesystem->touch($this->path . '/' . $filename);

        $path = $this->fixtureDir . DIRECTORY_SEPARATOR . $filename;

        $this->subject->upload($path, basename($path));

        $expectedCopiedFile = $this->path . DIRECTORY_SEPARATOR . 'foobar.txt';

        $this->assertTrue(file_exists($expectedCopiedFile));
        $this->assertSame(file_get_contents($path), file_get_contents($expectedCopiedFile));
    }


    public function testUploadWithDirectory()
    {
        $path = $this->fixtureDir;

        $this->subject->upload($path, basename($path));

        $this->assertTrue(is_dir($this->path . '/test-directory'));
        $this->assertTrue(is_dir($this->path . '/test-directory/barfoo'));
        $this->assertTrue(is_dir($this->path . '/test-directory/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/test-directory/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/test-directory/foobar/foobar.txt'));
    }

    public function testUploadWithDirectoryAndTargetPath()
    {
        $path = $this->fixtureDir;

        $this->subject->upload($path, 'foobar/test-directory');

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


    public function testCopyToFilesystemWithDirectory()
    {
        $subject          = $this->createFilesystem($this->fixtureDir);
        $targetFilesystem = $this->createFilesystem($this->path);

        $subject->copyToFilesystem('/', $targetFilesystem, 'new-directory');

        $this->assertTrue(is_dir($this->path . '/new-directory'));
        $this->assertTrue(is_dir($this->path . '/new-directory/barfoo'));
        $this->assertTrue(is_dir($this->path . '/new-directory/foobar'));
        $this->assertTrue(file_exists($this->path . '/new-directory/foobar.txt'));
        $this->assertTrue(file_exists($this->path . '/new-directory/barfoo/barfoo'));
        $this->assertTrue(file_exists($this->path . '/new-directory/barfoo/barfoo.txt'));
        $this->assertTrue(file_exists($this->path . '/new-directory/foobar/foobar'));
        $this->assertTrue(file_exists($this->path . '/new-directory/foobar/foobar.txt'));
    }

    public function testCopyToFilesystemWithFile()
    {
        $subject          = $this->createFilesystem($this->fixtureDir);
        $targetFilesystem = $this->createFilesystem($this->path);

        $subject->copyToFilesystem('barfoo/barfoo.txt', $targetFilesystem, 'barfoo.txt');

        $this->assertTrue(file_exists($this->path . '/barfoo.txt'));
        $this->assertFalse(is_dir($this->path . '/barfoo.txt'));
    }

    public function testGetSizeReturnsNullIfFileDoesNotExist()
    {
        $this->assertNull($this->subject->size('path/to/nowhere'));
    }

    public function testGetSizeReturnsSizeInBytes()
    {
        $subject          = $this->createFilesystem($this->fixtureDir);

        $this->assertTrue($subject->exists('foobar.txt'));
        $this->assertEquals(6, $subject->size('foobar.txt'));
    }


    /**
     * @param null $basePath
     * @param null $extension
     * @dataProvider provideCreateData
     */
    public function testCreateFile($basePath = null, $extension = null)
    {
        $path = $this->subject->create($basePath, $extension);

        $this->assertTrue(file_exists($this->path . '/' . $path));
        if($extension != null)
        {
            $this->assertEquals($extension, pathinfo($path, PATHINFO_EXTENSION));
        }
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

    public static function provideCreateData()
    {
        return array(
            array(),
            array(null, null),
            array('path/to/directory', 'txt'),
            array('path/to/directory/', 'txt'),
        );
    }
}