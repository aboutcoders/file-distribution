<?php
namespace Abc\Filesystem;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class Definition implements DefinitionInterface
{

    /**
     * @var string
     * @Type("string")
     * @Groups({"file"})
     */
    protected $type;

    /**
     * @var string
     * @Type("string")
     * @Groups({"file"})
     */
    protected $path;

    /**
     * @var array
     */
    protected $properties;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;
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

    /**
     * {@inheritdoc}
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }
}