<?php

namespace Abc\Filesystem;

interface FileInterface
{
    /**
     * @return DefinitionInterface
     */
    public function getFilesystemDefinition();

    /**
     * @param DefinitionInterface $definition
     */
    public function setFilesystemDefinition(DefinitionInterface $definition);

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