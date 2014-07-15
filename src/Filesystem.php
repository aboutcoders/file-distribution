<?php

namespace Abc\Filesystem;

use Gaufrette\Adapter;
use Gaufrette\Filesystem as BaseFilesystem;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class Filesystem extends BaseFilesystem
{

    /** @var AdapterFactoryInterface */
    protected $adapterFactory;
    /** @var DefinitionInterface */
    protected $definition;
    /** @var LoggerInterface */
    protected $logger;


    /**
     * @param AdapterFactoryInterface $adapterFactory
     * @param DefinitionInterface     $definition
     * @param LoggerInterface|null        $logger
     */
    function __construct(AdapterFactoryInterface $adapterFactory, DefinitionInterface $definition, LoggerInterface $logger = null)
    {
        $this->adapterFactory = $adapterFactory;
        $this->definition     = $definition;
        $this->logger         = $logger == null ? new NullLogger() : $logger;

        $properties = $definition->getProperties() == null ? array() : $definition->getProperties();

        $adapter = $this->adapterFactory->create($definition->getType(), $definition->getPath(), $properties);

        parent::__construct($adapter);
    }


    /**
     * @param string $path The path to a directory on this filesystem
     * @param bool $create Whether to create the directory if it does not exist
     * @return Filesystem
     * @throws \InvalidArgumentException If create is false and the directory with the given path does not exist
     */
    public function createClient($path, $create = false)
    {
        if(!$this->has($path))
        {
            if(!(bool)$create)
            {
                throw new \InvalidArgumentException(sprintf('The path "%s" does not exist not the filesystem and create is false', $path));
            }

            $this->mkdir($path);
        }

        $definition = clone $this->definition;
        $definition->setPath($this->definition->getPath() . '/' . $this->stripSlashes($path));

        return new Filesystem($this->adapterFactory, $definition, $this->logger);
    }


    /**
     * Uploads the given file or directory to the filesystem
     *
     * @param string      $localPath Adds the file or directory with the given path to the filesystem
     * @param string|null $remotePath The path on the filesystem
     * @param bool        $overwrite Whether to overwrite the path on the filesystem if it already exists (false by default)
     * @return void
     * @throws \Gaufrette\Exception\FileAlreadyExists If the file or directory already exists on the filesystem and overwrite is false
     * @throws \InvalidArgumentException If the file or directory specified by $localPath does not exist
     */
    public function upload($localPath, $remotePath = null, $overwrite = false)
    {
        if(!file_exists((string)$localPath))
        {
            throw new \InvalidArgumentException(sprintf('The path "%s" does not specify a file or directory', $localPath));
        }

        $remotePath = $this->stripTrailingSlash($remotePath) . '/' . basename($localPath);

        if($this->has($remotePath) && $overwrite)
        {
            $this->delete($remotePath);
        }

        if(!is_dir($localPath))
        {
            $this->write($remotePath, file_get_contents($localPath));
        }
        else
        {
            $this->mkdir($remotePath);
            $this->addDirectoryContents($localPath, $remotePath);
        }
    }

    /**
     * Downloads a file or directory
     *
     * @param string $remotePath The path to the remote file or directory to download
     * @param string $localDirectory The path to the local directory where the remote file or directory is downloaded to
     * @param int    $mode The directory mode of created directories (default is 0777)
     * @return void
     * @throws \InvalidArgumentException If the local directory is not writable
     * @throws \Gaufrette\Exception\FileNotFound If the file or directory does not exist on the filesystem
     */
    public function download($remotePath = '/', $localDirectory, $mode = 0777)
    {
        $remotePath = $this->stripTrailingSlash($remotePath);

        if(!is_dir($localDirectory))
        {
            throw new \InvalidArgumentException(sprintf('The path "%s" does not specify a directory', $localDirectory));
        }
        if(!is_writable($localDirectory))
        {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable', $localDirectory));
        }

        $localDirectory = $this->stripTrailingSlash($localDirectory);

        if(!$this->getAdapter()->isDirectory($remotePath))
        {
            file_put_contents($localDirectory . '/' . basename($remotePath), $this->get($remotePath)->getContent());
        }
        else
        {
            $filesystem = new BaseFilesystem(new Adapter\Local($localDirectory, true, $mode));

            $keys = $this->listKeys($remotePath);
            foreach($keys['keys'] as $key)
            {
                $filesystem->write($key, $this->get($key)->getContent());
            }
        }
    }

    /**
     * Creates a directory on the filesystem
     *
     * @param string $path The path to the directory on the filesystem
     * @return string the absolute path to the created directory
     * @throws \Gaufrette\Exception\FileAlreadyExists When file already exists and overwrite is false
     * @throws \RuntimeException When for any reason content could not be written
     */
    public function mkdir($path)
    {
        $path = $this->stripTrailingSlash($path);

        $tmp = $path . '/.init';
        $this->write($tmp, '');
        $this->delete($tmp);

        return $this->stripTrailingSlash($this->definition->getPath()) . '/' . $path;
    }

    /**
     * @param string      $localPath
     * @param string|null $remotePath
     */
    private function addDirectoryContents($localPath, $remotePath = null)
    {
        $iterator = new \FilesystemIterator($localPath);
        foreach($iterator as $fileInfo)
        {
            /** @var \SplFileInfo $fileInfo */
            $localPath = $remotePath . '/' . $fileInfo->getBasename();

            if($fileInfo->isDir())
            {
                $this->mkdir($localPath);
                $this->addDirectoryContents($fileInfo->getPathname(), $localPath);
            }
            else
            {
                $this->write($localPath, file_get_contents($fileInfo->getPathname()));
            }
        }
    }

    /**
     * @param $path
     * @return string
     */
    public function stripSlashes($path)
    {
        return $this->stripLeadingSlash($this->stripTrailingSlash($path));
    }

    /**
     * @param string $path
     * @return string
     */
    private function stripLeadingSlash($path)
    {
        $lastStr = substr($path, strlen($path) - 1);
        if($lastStr == '/' || $lastStr == '\\')
        {
            return substr($path, 0, strlen($path) - 1);
        }

        return $path;
    }

    /**
     * @param string $path
     * @return string
     */
    private function stripTrailingSlash($path)
    {
        $lastStr = substr($path, strlen($path) - 1);
        if($lastStr == '/' || $lastStr == '\\')
        {
            return substr($path, 0, strlen($path) - 1);
        }

        return $path;
    }
}