<?php


namespace Abc\Filesystem;

use Abc\Filesystem\DefinitionInterface;
use Abc\Filesystem\FilesystemInterface;

interface FilesystemFactoryInterface {

    /**
     * @param DefinitionInterface $definition
     * @return FilesystemInterface
     */
    public function create(DefinitionInterface $definition);

} 