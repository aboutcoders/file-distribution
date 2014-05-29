<?php

namespace Abc\File\Client;

use Abc\File\File;
use Abc\File\FileSystemOperatorInterface;
use Abc\File\Node;

class FileSystemStorageClient implements FileSystemOperatorInterface
{

    protected $folderPermission;
    protected $filePermission;
    protected $baseUrl;

    /**
     * Constructor.
     *
     * @param string $baseUrl          The url or path to a writable directory on the local filesystem
     * @param int    $folderPermission The permission created folders (default is 0755)
     * @param int    $filePermission   The permission created files (default is 0755)
     * @throws \Exception If the first argument does not specify a writable directory
     */
    public function __construct($baseUrl, $folderPermission = 0755, $filePermission = 0755)
    {
        // strip trailing slash
        $lastStr = substr($baseUrl, strlen($baseUrl) - 1);
        if ($lastStr == '/') {
            $baseUrl = substr($baseUrl, 0, strlen($baseUrl) - 1);
        }

        if (!is_dir($baseUrl)) {
            throw new \Exception(sprintf('The directory "%s" does not exist.', $baseUrl));
        }
        if (!is_writable($baseUrl)) {
            throw new \Exception(sprintf('The directory "%s" is not writable.', $baseUrl));
        }

        if (strpos($baseUrl, 'file://') !== 0) {
            $baseUrl = 'file://' . $baseUrl;
        }

        $this->baseUrl          = str_replace('\\', '/', $baseUrl);
        $this->folderPermission = $folderPermission;
        $this->filePermission   = $filePermission;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }


    /**
     * @param $remoteUrl
     * @param $localFilePath
     * @throws StorageException
     * @return string
     */
    public function download($remoteUrl, $localFilePath)
    {
        $this->validateUrl($localFilePath);
        $this->validateStorageUrl($remoteUrl);
        if (!$this->exists($remoteUrl)) {
            throw new StorageException(sprintf('The resource "%s" does not exist.', $remoteUrl));
        }

        $this->copy($remoteUrl, $localFilePath);
    }

    /**
     * @param string $localFilePath
     * @param string $remoteUrl
     * @throws StorageException
     * @return void
     */
    public function upload($localFilePath, $remoteUrl)
    {
        $this->validateUrl($localFilePath);
        $this->validateStorageUrl($remoteUrl);
        if (!file_exists($localFilePath)) {
            throw new StorageException(sprintf('The resource "%s" does not exist.', $remoteUrl));
        }

        $this->copy($localFilePath, $remoteUrl);
    }

    /**
     * @param string $url
     * @return boolean
     */
    public function exists($url)
    {
        return file_exists($url);
    }

    /**
     * Copy a file
     *
     * @param string $sourceUrl
     * @param string $targetUrl
     * @throws StorageException
     */
    protected function copy($sourceUrl, $targetUrl)
    {
        // verify that the target directory is not located in the source directory
        if (strpos($targetUrl, $sourceUrl) === 0) {
            throw new StorageException('The target directory is located in the source directory');
        }

        if (is_dir($sourceUrl)) {
            if (is_dir($targetUrl)) {
                throw new StorageException(sprintf('The directory "%s" already exists.', $targetUrl));
            }

            $this->copyDir($sourceUrl, $targetUrl);
        } else {
            if (file_exists($targetUrl)) {
                throw new StorageException(sprintf('The file "%s" already exists.', $targetUrl));
            }

            $this->copyFile($sourceUrl, $targetUrl);
        }
    }

    /**
     * Copy a file.
     *
     * @param $source
     * @param $destination
     * @throws StorageException
     */
    protected function copyFile($source, $destination)
    {
        if (@copy($source, $destination) === false) {
            throw new StorageException(sprintf('Failed to copy the file "%s" to %s.', $source, $destination));
        }
        @chmod($destination, $this->filePermission);
    }

    /**
     * Copy a directory.
     *
     * @param string $sourceUrl
     * @param string $targetUrl
     * @throws StorageException
     */
    protected function copyDir($sourceUrl, $targetUrl)
    {
        if (@mkdir($targetUrl, $this->folderPermission, true) === false) {
            throw new StorageException(sprintf('Failed to create the directory "%s".', $targetUrl));
        }

        foreach ($this->doFetchFiles($sourceUrl) as $netStorageFile) {
            $this->copyFile($netStorageFile->getPath(), $targetUrl . '/' . $netStorageFile->getName());
        }

        foreach ($this->doFetchNodes($sourceUrl) as $netStorageNode) {
            $this->copyDir($netStorageNode->getPath(), $targetUrl . '/' . $netStorageNode->getName());
        }
    }

    /**
     * Fetch all resources of type node (directories, folders).
     *
     * @param string $url The url of a node
     * @return array NetStorageNode[] An array of NetStorageNode objects
     */
    protected function doFetchNodes($url)
    {
        $lastStr = substr($url, strlen($url) - 1);
        if ($lastStr != '/') {
            $url .= '/';
        }

        $nodes = array();
        foreach (scandir($url) as $entry) {
            if ($entry != '.' && $entry != '..' && is_dir($url . $entry)) {
                $nodes[] = new Node($entry, $url . $entry);
            }
        }

        return $nodes;
    }

    /**
     * Fetch all resources of type file.
     *
     * @param string $url The url of a node
     * @return array NetStorageFile[] An array of NetStorageFile objects
     * @throws StorageException
     */
    protected function doFetchFiles($url)
    {
        $lastStr = substr($url, strlen($url) - 1);
        if ($lastStr != '/') {
            $url .= '/';
        }

        $files = array();
        foreach (scandir($url) as $entry) {
            if ($entry != '.' && $entry != '..' && !is_dir($url . $entry)) {
                $files[] = new File($entry, $url . $entry, filesize($url . $entry));
            }
        }

        return $files;
    }

    /**
     * Validates if a string is a valid URL (Uniform Resource Locator, RFC 1738)
     *
     * @param string $url The string to be validated
     * @throws \InvalidArgumentException
     */
    protected function validateUrl($url)
    {
        if (!preg_match('((file)|(vfs):((//)|(\\\\))[\w\d:#%/;$()~_?\\-=\\\.&]*)', $url)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL.', $url));
        }
    }

    /**
     * Verifies that the url is within the scope of the storage.
     *
     * @param string $url
     * @throws StorageException If validation fails
     */
    protected function validateStorageUrl($url)
    {
        $this->validateUrl($url);

        // verify that the url is within the base url
        if (strpos($url, $this->baseUrl) !== 0) {
            throw new StorageException(sprintf('The url "%s" is outside of the scope of "%s".', $url, $this->baseUrl));
        }
    }
}