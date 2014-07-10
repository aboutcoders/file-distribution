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
     * @return FilesystemInterface
     */
    public function getLocation();

    /**
     * @param FilesystemInterface $location
     */
    public function setLocation(FilesystemInterface $location);

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize);

}