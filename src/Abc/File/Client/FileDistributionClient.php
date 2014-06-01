<?php

namespace Abc\File\Client;

use Abc\File\DistributionManagerInterface;
use Abc\File\LocationInterface;
use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\File;

class FileDistributionClient implements DistributionManagerInterface
{

    /**
     * @param File              $file
     * @param LocationInterface $location
     * @param boolean           $overwrite Whether to overwrite the file if exists
     * @throws FileAlreadyExists When file already exists and overwrite is false
     * @throws \RuntimeException When for any reason content could not be written
     *
     * @return integer The number of bytes that were written into the file
     */
    public function distribute(File $file, LocationInterface $location, $overwrite = false)
    {
        $filesystem = $location->getFilesystem();
        return $filesystem->write($file->getName(), $file->getContent(), $overwrite);
    }
}