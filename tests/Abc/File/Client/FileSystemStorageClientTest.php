<?php

use Abc\File\Client\FileSystemStorageClient;

class FileSystemStorageClientTest extends PHPUnit_Framework_TestCase
{
    /** @var FileSystemStorageClient */
    protected $subject;
    /** @var string */
    protected $testFile;

    protected function setUp()
    {
        parent::setUp();

        $this->testFile = 'file:///tmp/test.txt';
        touch($this->testFile);
        $this->subject = new FileSystemStorageClient('/tmp');
    }

    protected function tearDown()
    {
        parent::tearDown();
        unlink($this->testFile);
    }


    public function testConstructorStripsTrailingSlashOfBaseUrl()
    {
        $subject = new FileSystemStorageClient('/tmp/');
        $this->assertStringEndsNotWith('/', $subject->getBaseUrl(), 'Trailing slash is not removed');
    }

    /**
     * @expectedException \Exception
     */
    public function testConstructorWithBasePathNotDirectoryThrowsException()
    {
        new FileSystemStorageClient($this->testFile);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The directory "/tmp/notWritable" is not writable.
     */
    public function testConstructorWithNotWritableBasePathThrowsException()
    {
        $notWritableDir = '/tmp/notWritable';
        if (!file_exists($notWritableDir)) {
            mkdir($notWritableDir, 0111);
        }
        new FileSystemStorageClient($notWritableDir);
    }

    public function testExistForExistingUrlReturnsTrue()
    {
        $return = $this->subject->exists($this->testFile);

        $this->assertTrue($return, 'Result is not as expected');
    }

    public function testExistForNotExistingUrlReturnsFalse()
    {
        $url    = 'file:///test1.txt';
        $return = $this->subject->exists($url);
        $this->assertFalse($return, 'Result is not as expected');
    }

    public function testDownloadWithValidPathsCopiesFile()
    {
        $destinationDir = '/tmp/download/';
        if (!file_exists($destinationDir))
            mkdir($destinationDir);
        $targetUrl = 'file://' . $destinationDir . 'abc.txt';

        $this->subject->download($this->testFile, $targetUrl);
        $this->assertFileExists($targetUrl, 'Expected file does not exist');
        unlink($targetUrl);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     */
    public function testDownloadWithNotExistingFileThrowsException()
    {
        $localFilePath = 'file:///test1.txt';
        $remoteUrl     = 'file:///tmp/copy/abc.txt';

        $this->subject->download($remoteUrl, $localFilePath);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     * @expectedExceptionMessage Failed to copy the file "file:///tmp/test.txt" to file:///readOnly/test1.txt.
     */
    public function testCopyFileThrowsException()
    {
        $localFilePath = 'file:///readOnly/test1.txt';

        $this->subject->download($this->testFile, $localFilePath);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage "/readOnly/test1.txt" is not a valid URL.
     */
    public function testValidateUrlThrowsException()
    {
        $testFile = '/readOnly/test1.txt';

        $this->subject->download($testFile, $testFile);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     * @expectedExceptionMessage The url "file://test/readOnly/test1.txt" is outside of the scope of "file:///tmp".
     */
    public function testValidateStorageUrlThrowsException()
    {
        $testFile = 'file://test/readOnly/test1.txt';

        $this->subject->download($testFile, $testFile);
    }
    
    public function testUploadWithValidPathsCopiesFile()
    {
        $destinationDir = '/tmp/copy/';
        if (!file_exists($destinationDir))
            mkdir($destinationDir);
        $remoteUrl = 'file:///tmp/copy/abc.txt';

        $this->subject->upload($this->testFile, $remoteUrl);
        $this->assertFileExists($remoteUrl, 'Expected file does not exist');
        unlink($remoteUrl);
    }

    public function testUploadWithValidPathsCopiesDirectory()
    {

        $sourceDir      = 'file:///tmp/copyFrom';
        $destinationDir = 'file:///tmp/copyTo';

        if (!is_dir($sourceDir)) {
            mkdir($sourceDir);
        }

        if (!is_dir($sourceDir . '/subDir')) {
            mkdir($sourceDir . '/subDir');
        }
        touch($sourceDir . '/test1.txt');
        touch($sourceDir . '/test2.txt');
        touch($sourceDir . '/subDir/test1.txt');

        $this->subject->upload($sourceDir, $destinationDir);
        $this->assertFileExists($destinationDir . '/test1.txt');
        $this->assertFileExists($destinationDir . '/test2.txt');
        $this->assertFileExists($destinationDir . '/subDir/test1.txt');

        //Cleanup
//        $this->delTree($sourceDir);
//        $this->delTree('/tmp/copyTo');

    }

    /**
     * @expectedException Abc\File\Client\StorageException
     */
    public function testUploadWithNotExistingFileThrowsException()
    {
        $localFilePath = 'file:///test1.txt';
        $remoteUrl     = 'file:///tmp/copy/abc.txt';

        $this->subject->upload($localFilePath, $remoteUrl);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     * @expectedExceptionMessage The target directory is located in the source directory
     */
    public function testUploadWithSamePathThrowsException()
    {
        $this->subject->upload($this->testFile, $this->testFile);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     * @expectedExceptionMessage The file "file:///tmp/test2.txt" already exists.
     */
    public function testUploadWithExistingFileAtTargetLocationThrowsException()
    {
        $targetFile = 'file:///tmp/test2.txt';
        touch($targetFile);
        $this->subject->upload($this->testFile, $targetFile);
        unlink($targetFile);
    }

    /**
     * @expectedException Abc\File\Client\StorageException
     * @expectedExceptionMessage The directory "file:///tmp/existingDirectory" already exists.
     */
    public function testUploadWithExistingDirectoryThrowsException()
    {
        $sourceDirectory = 'file:///tmp/sourceDirectory';
        $targetDirectory = 'file:///tmp/existingDirectory';
        if (!is_dir($sourceDirectory)) {
            mkdir($sourceDirectory);
        }
        if (!is_dir($sourceDirectory)) {
            mkdir($sourceDirectory);
        }
        $this->subject->upload($sourceDirectory, $targetDirectory);
        echo rmdir($sourceDirectory);
        echo rmdir($targetDirectory);
    }

    protected function delTree($directory)
    {
        if (is_dir($directory)) {
            $files = glob($directory . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned

            foreach ($files as $file) {
                $this->delTree($file);
            }
            rmdir($directory);
        } elseif (is_file($directory)) {
            unlink($directory);
        }
    }
}
 