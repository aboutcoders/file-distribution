<?php

namespace Abc\File;


interface LocationInterface
{

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @return array
     */
    public function getProperties();

    /**
     * @param array $properties
     */
    public function setProperties(array $properties);

}