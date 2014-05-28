<?php
namespace Abc\File;
class Node
{

    protected $name;
    protected $path;

    /**
     * Constructor.
     *
     * @param string $name Name of the node
     * @param string $path Path of the node
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Get the name of the file.
     *
     * @return string The name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the path of the node.
     *
     * @return string The path
     */
    public function getPath()
    {
        return $this->path;
    }
}