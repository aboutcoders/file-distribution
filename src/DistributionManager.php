<?php

namespace Abc\File;

use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\File;

class DistributionManager implements DistributionManagerInterface
{
    /** @var AdapterFactory */
    protected $filesystemFactory;

    /**
     * @param AdapterFactory $filesystemFactory
     */
    function __construct(AdapterFactory $filesystemFactory)
    {
        $this->filesystemFactory = $filesystemFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function distribute(FileInterface $file, FilesystemInterface $filesystem)
    {
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($file->getFilesystem());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($filesystem);

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
        $sourceFilesystem = $this->filesystemFactory->buildFilesystem($sourceFile->getFilesystem());
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($targetFile->getFilesystem());
        $file             = new File($sourceFile->getPath(), $sourceFilesystem);

        return $targetFilesystem->write($targetFile->getPath(), $file->getContent(), $overwrite);
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(FilesystemInterface $filesystem)
    {
        $targetFilesystem = $this->filesystemFactory->buildFilesystem($filesystem);
        $file             = $targetFilesystem->createFile(uniqid());
        $targetFile       = new \Abc\File\File($file->getName(), $file->getKey(), $file->getSize());

        return $targetFile;
    }

    /**
     * {@inheritdoc}
     */
    public function createFilesystem(FilesystemInterface $filesystem, $directoryName)
    {
        $filesystem->setPath($directoryName);
        $filesystem->setType(FilesystemType::Filesystem);
        $filesystem->setProperties(array('create' => true));

        $targetFilesystem = $this->filesystemFactory->buildFilesystem($filesystem);
        if(!$targetFilesystem->has($filesystem->getPath()))
        {
            $targetFilesystem->write('.init', '');
        }

        return $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FileInterface $file)
    {
        $filesystem = $this->filesystemFactory->buildFilesystem($file->getFilesystem());

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

        $filesystem = $this->filesystemFactory->buildFilesystem($file->getFilesystem());

        return $filesystem->has($path);
    }
}