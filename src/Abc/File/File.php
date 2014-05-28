<?php
/**
 * Created by PhpStorm.
 * User: wojciechciolko
 * Date: 28.05.2014
 * Time: 16:57
 */

namespace Abc\File;


class File implements FileInterface
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $path;
    /** @var LocationInterface */
    protected $location;
    /** @var int */
    protected $fileSize;

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
     * @return mixed
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * @param mixed $fileSize
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param LocationInterface $location
     */
    public function setLocation(LocationInterface $location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


}