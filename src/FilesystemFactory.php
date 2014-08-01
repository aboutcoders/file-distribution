<?php

namespace Abc\Filesystem;

use Abc\Filesystem\AdapterFactoryInterface;
use Abc\Filesystem\DefinitionInterface;
use Abc\Filesystem\FilesystemInterface;

/**
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class FilesystemFactory implements FilesystemFactoryInterface
{
    /** @var AdapterFactoryInterface */
    protected $adapterFactory;

    /**
     * @param AdapterFactoryInterface $adapterFactory
     */
    function __construct(AdapterFactoryInterface $adapterFactory)
    {
        $this->adapterFactory = $adapterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(DefinitionInterface $definition)
    {
        return new Filesystem($this->adapterFactory, $definition);

    }
}