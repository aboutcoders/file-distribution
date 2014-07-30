<?php
namespace Abc\Filesystem;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class File implements FileInterface
{
    /**
     * @var string
     * @Type("string")
     * @Groups("file")
     */
    protected $path;

    /**
     * @var DefinitionInterface
     * @Type("Abc\Filesystem\Definition")
     * @Groups("file")
     */
    protected $definition;

    /**
     * @var
     * @Type("integer")
     * @Groups({"file"})
     */
    protected $size;

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($bytes)
    {
        $this->size = $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystemDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilesystemDefinition(DefinitionInterface $definition)
    {
        $this->definition = $definition;
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