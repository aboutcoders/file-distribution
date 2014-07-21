<?php

namespace Abc\Filesystem;

/**
 * FilesystemInterface
 */
interface FilesystemInterface
{

    /**
     * @return DefinitionInterface
     */
    public function getDefinition();

    /**
     * Creates a filesystem
     *
     * @param string $path The path to a directory on this filesystem
     * @param bool   $create Whether to create the directory if it does not exist
     * @return FilesystemInterface
     * @throws \InvalidArgumentException If create is false and the directory with the given path does not exist
     * @throws \RuntimeException
     */
    public function createFilesystem($path, $create = false);

    /**
     * @param string              $path The path to a file or directory on the filesystem
     * @param FilesystemInterface $targetFilesystem The filesystem where the data is copied to
     * @param string              $targetPath The path to a file or directory on the target filesystem
     * @throws \RuntimeException
     */
    public function copyToFilesystem($path, FilesystemInterface $targetFilesystem, $targetPath);

    /**
     * Uploads a file or directory
     *
     * @param string $localPath The path to a file or directory on the local filesystem
     * @param string $remotePath The path to a file or directory on the remote filesystem
     * @param bool   $overwrite Whether to overwrite the path on the remote filesystem if it already exists (false by default)
     * @return void
     * @throws \Gaufrette\Exception\FileAlreadyExists If the file or directory already exists on the filesystem and overwrite is false
     * @throws \InvalidArgumentException If the file or directory specified by $localPath does not exist
     * @throws \RuntimeException
     */
    public function upload($localPath, $remotePath, $overwrite = false);

    /**
     * Downloads a file or directory
     *
     * @param string $remotePath The path to a file or directory on the remote filesystem
     * @param string $localPath The path to a directory on the local filesystem where the remote file or directory is downloaded to
     * @param int    $mode The directory mode of created directories (default is 0777)
     * @return void
     * @throws \InvalidArgumentException If the local directory is not writable
     * @throws \Gaufrette\Exception\FileNotFound If the file or directory does not exist on the filesystem
     * @throws \RuntimeException
     */
    public function download($remotePath = '/', $localPath, $mode = 0777);

    /**
     * Creates a directory
     *
     * @param string $path The path to the directory on the filesystem
     * @return string the absolute path to the created directory
     * @throws \Gaufrette\Exception\FileAlreadyExists When file already exists and overwrite is false
     * @throws \RuntimeException When for any reason content could not be written
     */
    public function mkdir($path);

    /**
     * Whether the a file or directory exists
     *
     * @param string $path The path to a directory on the filesystem
     * @return boolean
     */
    public function exists($path);

    /**
     * Deletes a file or directory
     *
     * @param string $path The path to the directory on the filesystem
     * @throws \RuntimeException
     */
    public function remove($path);
} 