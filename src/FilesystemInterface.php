<?php

namespace Abc\Filesystem;

use Psr\Log\LoggerInterface;

/**
 * FilesystemInterface
 */
interface FilesystemInterface
{

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);

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
     * @param bool                $overwrite Whether to overwrite the file defined by $targetPath
     * @throws \RuntimeException
     */
    public function copyToFilesystem($path, FilesystemInterface $targetFilesystem, $targetPath, $overwrite = false);

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
     * @param string $path The path to the directory to create
     * @return string the absolute path to the created directory
     * @throws \Gaufrette\Exception\FileAlreadyExists When file already exists and overwrite is false
     * @throws \RuntimeException When for any reason content could not be written
     */
    public function mkdir($path);

    /**
     * Creates an empty file
     *
     * @param string $basePath The base path to a directory where the file will be created
     * @param string|null $fileExtension The file extension of the file to create
     * @return string The path to the created file
     * @throws \Gaufrette\Exception\FileAlreadyExists When file already exists
     * @throws \RuntimeException When for any creation of the file fails
     */
    public function create($basePath = '/', $fileExtension = null);

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
     * If the root path ("/" or "") all contents of the filesystem will be removed (truncated).
     *
     * @param string $path The path to the directory
     * @throws \RuntimeException
     */
    public function remove($path);

    /**
     * Destroys the filesystem
     * @return void
     * @throws \RuntimeException If destroying the filesystem fails
     */
    public function destroy();

    /**
     * Returns the size of a file in bytes
     *
     * @param string $path The path to the file
     * @return int|null The file size in bytes, null if the file or directory does not exist
     * @throws \RuntimeException
     */
    public function size($path);
} 