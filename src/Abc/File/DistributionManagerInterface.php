<?php
namespace Abc\File;

use Gaufrette\Exception\FileAlreadyExists;

interface DistributionManagerInterface
{

    /**
     * @param \Gaufrette\File   $file
     * @param LocationInterface $location
     * @param boolean           $overwrite Whether to overwrite the file if exists
     * @throws FileAlreadyExists When file already exists and overwrite is false
     * @throws \RuntimeException When for any reason content could not be written
     *
     * @return integer The number of bytes that were written into the file
     */
    public function distribute(\Gaufrette\File $file, LocationInterface $location, $overwrite);
} 