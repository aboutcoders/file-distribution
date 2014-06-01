<?php
namespace Abc\File;

interface DistributionManagerInterface
{

    /**
     * @param \Gaufrette\File   $file
     * @param LocationInterface $location
     * @return FileInterface
     */
    public function distribute(\Gaufrette\File $file, LocationInterface $location);
} 