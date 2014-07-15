<?php
namespace Abc\Filesystem;


class File implements FileInterface
{
    /** @var string */
    protected $path;
    /** @var DefinitionInterface */
    protected $filesystem;
    /** @var int */
    protected $fileSize;

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->fileSize;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($bytes)
    {
        $this->fileSize = $bytes;
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
    public function setFilesystem(DefinitionInterface $filesystem)
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