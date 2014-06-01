<?php

namespace Abc\File\Client;

use Abc\File\DistributionManagerInterface;
use Abc\File\LocationInterface;
use Gaufrette\File;

class FileDistributionClient implements DistributionManagerInterface
{

    /**
     * @param File              $file
     * @param LocationInterface $location
     * @return int The number of bytes that were written into the file
     */
    public function distribute(File $file, LocationInterface $location)
    {
        $filesystem = $location->getFilesystem();
        return $filesystem->write($file->getName(), $file->getContent());
    }
}