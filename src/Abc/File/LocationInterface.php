<?php

namespace Abc\File;

interface LocationInterface
{

    /**
     * @return string
     */
    public function getType();

    /**
     * @param LocationTypeInterface $type
     */
    public function setType(LocationTypeInterface $type);

    /**
     * @return string
     */
    public function getUrl();

    public function setUrl($url);

    /**
     * @return array
     */
    public function getProperties();

    /**
     * @param array $properties
     */
    public function setProperties(array $properties);
    
}