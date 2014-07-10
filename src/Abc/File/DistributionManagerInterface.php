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
     * Distributes file to a location
     *
     * @param FileInterface     $file
     * @param FilesystemInterface $location
     * @return FileInterface
     */
    public function distribute(FileInterface $file, FilesystemInterface $location);

    /**
     * Creates new file
     *
     * @param FilesystemInterface $location
     * @return FileInterface
     */
    public function createFile(FilesystemInterface $location);

    /**
     * @param FilesystemInterface $location
     * @param string            $directoryName
     * @return FilesystemInterface
     */
    public function createLocation(FilesystemInterface $location, $directoryName);

    /**
     * Deletes file from a location
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function delete(FileInterface $file);

    /**
     * Deletes file from a location
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function exists(FileInterface $file);
} 