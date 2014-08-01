<?php

namespace Abc\Filesystem;

class DefinitionFactory
{

    /**
     * {@inheritdoc}
     */
    public function create($type, $path, array $properties = array())
    {
        $definition = new Definition();
        $definition->setType($type);
        $definition->setPath($path);
        $definition->setProperties($properties);

        return $definition;
    }
} 