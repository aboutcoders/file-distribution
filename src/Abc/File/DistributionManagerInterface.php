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

    /**
     * Creates new file
     *
     * @param FilesystemInterface $filesystem
     * @return FileInterface
     */
    public function createFile(FilesystemInterface $filesystem);

    /**
     * @param FilesystemInterface $filesystem
     * @param string            $directoryName
     * @return FilesystemInterface
     */
    public function createFilesystem(FilesystemInterface $filesystem, $directoryName);

    /**
     * Deletes file from a filesystem
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function delete(FileInterface $file);

    /**
     * Deletes file from a filesystem
     *
     * @param FileInterface $file
     * @return boolean
     */
    public function exists(FileInterface $file);
} 