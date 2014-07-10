<?php
namespace Abc\File;


class File implements FileInterface
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $path;
    /** @var FilesystemInterface */
    protected $filesystem;
    /** @var int */
    protected $fileSize;

    /**
     * @param string            $name
     * @param string            $path
     * @param int               $fileSize
     * @param FilesystemInterface $filesystem
     */
    function __construct($name, $path, $fileSize, $filesystem = null)
    {
        $this->name     = $name;
        $this->path     = $path;
        $this->fileSize = $fileSize;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}