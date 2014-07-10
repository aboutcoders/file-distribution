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
    public function getFilesystem();

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem);

    /**
     * @return int
     */
    public function getFileSize();

    /**
     * @param int $fileSize
     */
    public function setFileSize($fileSize);

}