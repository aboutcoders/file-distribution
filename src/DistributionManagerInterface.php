<?php
namespace Abc\File;

interface DistributionManagerInterface
{

    /**
     * Copies file A to file B
     *
     * @param FileInterface $sourceFile
     * @param FileInterface $targetFile
     * @param boolean       $overwrite
     * @return
     */
    public function copyFile(FileInterface $sourceFile, FileInterface $targetFile, $overwrite);


    /**
     * Distributes file to a filesystem
     *
     * @param FileInterface     $file
     * @param FilesystemInterface $filesystem
     * @return FileInterface
     */
    public function distribute(FileInterface $file, FilesystemInterface $filesystem);
} 