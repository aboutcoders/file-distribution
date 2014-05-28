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
     * @return int
     */
    public function getFileSize();

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize);

}