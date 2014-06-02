<?php

namespace Abc\File;

use Abc\File\Exception\FilesystemException;
use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\File;
use Gaufrette\Filesystem;

class DistributionManager implements DistributionManagerInterface
{

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
        $sourceFilesystem = $this->buildFilesystem($file->getLocation());
        $targetFilesystem = $this->buildFilesystem($location);

        $sourceFile = new File($file->getPath(), $sourceFilesystem);
        $result     = $targetFilesystem->write($file->getPath(), $sourceFile->getContent());
        $targetFile = new \Abc\File\File($sourceFile->getName(), $sourceFile->getKey(), $result);
        return $targetFile;
    }

    /**
     * @param LocationInterface $location
     * @throws FilesystemException
     * @return Filesystem
     */
    private function buildFilesystem(LocationInterface $location)
    {
        return FilesystemFactory::build($location->getType(), $location->getUrl(), $location->getProperties());
    }

    /**
     * Distributes file A to file B
     *
     * @param FileInterface $sourceFile
     * @param FileInterface $targetFile
     * @param boolean       $overwrite
     * @throws FileAlreadyExists When file already exists and overwrite is false
     */
    public function copyFile(FileInterface $sourceFile, FileInterface $targetFile, $overwrite)
    {
        $sourceFilesystem = $this->buildFilesystem($sourceFile->getLocation());
        $targetFilesystem = $this->buildFilesystem($targetFile->getLocation());
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
        $targetFilesystem = $this->buildFilesystem($location);
        $file             = $targetFilesystem->createFile(uniqid());
        $targetFile       = new \Abc\File\File($file->getName(), $file->getKey(), $file->getSize());
        return $targetFile;
    }
}