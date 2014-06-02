<?php

namespace Abc\File;

use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\File;

class DistributionManager implements DistributionManagerInterface
{
    /** @var FilesystemFactory */
    protected $filesystemFactory;

    function __construct(FilesystemFactory $filesystemFactory)
    {
        $this->filesystemFactory = $filesystemFactory;
    }

    /**
     * Distributes file to a location
     *
     * @param FileInterface     $file
     * @param LocationInterface $location
     * @throws FileAlreadyExists When file already exists and overwrite is false
     * @return FileInterface
     */
    public function distribute(FileInterface $file, LocationInterface $location)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);

        $sourceFile = new File($file->getPath(), $sourceFilesystem);

        $result = $targetFilesystem->write($file->getPath(), $sourceFile->getContent());

        $targetFile = new \Abc\File\File($sourceFile->getName(), $sourceFile->getKey(), $result);
        return $targetFile;
    }

    /**
     * Distributes file A to file B
     *
     * @param FileInterface $sourceFile
     * @param FileInterface $targetFile
     * @param boolean       $overwrite
     * @throws FileAlreadyExists When file already exists and overwrite is false
     */
    public function copyFile(FileInterface $sourceFile, FileInterface $targetFile, $overwrite = false)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($sourceFile->getLocation());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($targetFile->getLocation());
        $file             = new File($sourceFile->getPath(), $sourceFilesystem);

        $targetFilesystem->write($targetFile->getPath(), $file->getContent(), $overwrite);
    }

    /**
     * Creates new file
     *
     * @param LocationInterface $location
     * @return FileInterface
     */
    public function createFile(LocationInterface $location)
    {
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);
        $file             = $targetFilesystem->createFile(uniqid());
        $targetFile       = new \Abc\File\File($file->getName(), $file->getKey(), $file->getSize());
        return $targetFile;
    }
}