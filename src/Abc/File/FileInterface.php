<?php

namespace Abc\File;

interface FileInterface
{

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     * @return mixed
     */
    public function setPath($path);

    /**
     * @return LocationInterface
     */
    public function getLocation();

    /**
     * @param LocationInterface $location
     */
    public function setLocation(LocationInterface $location);

    /**
     * @return string
     */
    public function getFileSize();

    /**
     * @param string $fileSize
     */
    public function setFileSize($fileSize);

}