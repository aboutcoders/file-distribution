<?php
namespace Abc\File;


class File implements FileInterface
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $path;
    /** @var FilesystemInterface */
    protected $location;
    /** @var int */
    protected $fileSize;

    /**
     * @param string            $name
     * @param string            $path
     * @param int               $fileSize
     * @param FilesystemInterface $location
     */
    function __construct($name, $path, $fileSize, $location = null)
    {
        $this->name     = $name;
        $this->path     = $path;
        $this->fileSize = $fileSize;
        $this->location = $location;
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
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocation(FilesystemInterface $location)
    {
        $this->location = $location;
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