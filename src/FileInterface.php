<?php

namespace Abc\Filesystem;

interface FileInterface
{
    /**
     * @return DefinitionInterface
     */
    public function getFilesystem();

    /**
     * @param DefinitionInterface $filesystem
     */
    public function setFilesystem(DefinitionInterface $filesystem);

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