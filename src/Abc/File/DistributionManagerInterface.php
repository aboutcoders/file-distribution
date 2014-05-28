<?php
namespace Abc\File;

interface DistributionManagerInterface
{

    /**
     * @param FileInterface     $file
     * @param LocationInterface $location
     * @return FileInterface
     */
    public function distribute(FileInterface $file, LocationInterface $location);
} 