<?php

namespace Abc\File;

interface FileInterface
{
    /**
     * @return FilesystemInterface
     */
    public function getFilesystem();

    /**
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $bytes
     */
    public function setSize($bytes);
}