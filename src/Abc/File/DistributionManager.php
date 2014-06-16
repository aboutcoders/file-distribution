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
     * @return integer The number of bytes that were written into the file
     * @throws FileAlreadyExists When file already exists and overwrite is false
     */
    public function copyFile(FileInterface $sourceFile, FileInterface $targetFile, $overwrite = false)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($sourceFile->getLocation());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($targetFile->getLocation());
        $file             = new File($sourceFile->getPath(), $sourceFilesystem);

        return $targetFilesystem->write($targetFile->getPath(), $file->getContent(), $overwrite);
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

    /**
     * @param LocationInterface $location
     * @param string            $directoryName
     * @return LocationInterface
     */
    public function createLocation(LocationInterface $location, $directoryName)
    {
        $location->setPath($directoryName);
        $location->setType(FilesystemType::Filesystem);
        $location->setProperties(array('create' => true));

        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);
        if (!$targetFilesystem->has($location->getPath())) {
            $targetFilesystem->write('.init', '');
        }
        return $location;
    }

    /**
     * Deletes file from a location
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function delete(FileInterface $file)
    {
        $filesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());
        return $filesystem->delete($file->getPath());
    }



    /**
     * Deletes file from a location
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function exists(FileInterface $file)
    {
        $path = $file->getPath();
        if (empty($path)) {
            return false;
        }

        $filesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());
        return $filesystem->has($path);
    }
}