<?php

namespace Abc\Filesystem;

interface DefinitionFactoryInterface
{
    /**
     * @param string $type
     * @param string $path
     * @param array $properties
     * @return DefinitionInterface
     * @throws \InvalidArgumentException
     */
    public function create($type, $path, array $properties = array());
}