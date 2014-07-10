<?php

namespace Abc\File;

use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\File;

class DistributionManager implements DistributionManagerInterface
{
    /** @var FilesystemFactory */
    protected $filesystemFactory;

    /**
     * @param FilesystemFactory $filesystemFactory
     */
    function __construct(FilesystemFactory $filesystemFactory)
    {
        $this->filesystemFactory = $filesystemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function distribute(FileInterface $file, FilesystemInterface $location)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);

        $sourceFile = new File($file->getPath(), $sourceFilesystem);

        $result = $targetFilesystem->write($file->getPath(), $sourceFile->getContent());

        $targetFile = new \Abc\File\File($sourceFile->getName(), $sourceFile->getKey(), $result);

        return $targetFile;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile(FileInterface $sourceFile, FileInterface $targetFile, $overwrite = false)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($sourceFile->getLocation());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($targetFile->getLocation());
        $file             = new File($sourceFile->getPath(), $sourceFilesystem);

        return $targetFilesystem->write($targetFile->getPath(), $file->getContent(), $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(FilesystemInterface $location)
    {
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);
        $file             = $targetFilesystem->createFile(uniqid());
        $targetFile       = new \Abc\File\File($file->getName(), $file->getKey(), $file->getSize());

        return $targetFile;
    }

    /**
     * {@inheritdoc}
     */
    public function createLocation(FilesystemInterface $location, $directoryName)
    {
        $location->setPath($directoryName);
        $location->setType(FilesystemType::Filesystem);
        $location->setProperties(array('create' => true));

        $targetFilesystem = $this->filesystemFactory->buildFilesystem($location);
        if(!$targetFilesystem->has($location->getPath()))
        {
            $targetFilesystem->write('.init', '');
        }

        return $location;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FileInterface $file)
    {
        $filesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());

        return $filesystem->delete($file->getPath());
    }

    /**
     * {@inheritdoc}
     */
    public function exists(FileInterface $file)
    {
        $path = $file->getPath();

        if(empty($path))
        {
            return false;
        }

        $filesystem = $this->filesystemFactory->buildFilesystem($file->getLocation());

        return $filesystem->has($path);
    }
}