<?php

namespace Abc\Filesystem;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
interface AdapterFactoryInterface
{
    /**
     * @param string $type
     * @param string $path
     * @param array $options
     * @return \Gaufrette\Adapter
     * @throws \InvalidArgumentException
     */
    public function create($type, $path, array $options = array());
} 