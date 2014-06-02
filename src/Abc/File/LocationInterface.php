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
    public function getUrl();

    /**
     * @param string $url
     */
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