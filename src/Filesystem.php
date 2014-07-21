<?php

namespace Abc\Filesystem;

use Gaufrette\Adapter;
use Gaufrette\Filesystem as BaseFilesystem;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem as LocalFilesystem;

class Filesystem extends BaseFilesystem implements FilesystemInterface
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
     * @param LoggerInterface|null    $logger
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
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function createFilesystem($path, $create = false)
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
     * {@inheritdoc}
     */
    public function copyToFilesystem($path, FilesystemInterface $targetFilesystem, $targetPath)
    {
        $tempDir = $this->stripTrailingSlash(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . sha1(uniqid(mt_rand(), true));

        if(!@mkdir($tempDir))
        {
            $error = error_get_last();
            throw new \RuntimeException(sprintf('Failed to create directory %s (%s)', $tempDir, strip_tags($error['message'])));
        }

        try
        {
            $this->download($path, $tempDir);

            $targetFilesystem->upload($tempDir, $targetPath);

            $this->getLocalFilesystem()->remove($tempDir);
        }
        catch(\Exception $e)
        {
            $this->getLocalFilesystem()->remove($tempDir);

            throw $e;
        }
    }

    /**
     * Whether the a file or directory exists
     *
     * @param string $path The path to a directory on the filesystem
     * @return boolean
     */
    public function exists($path)
    {
        return $this->has($path);
    }

    /**
     * {@inheritdoc}
     */
    public function upload($localPath, $remotePath, $overwrite = false)
    {
        if(!file_exists((string)$localPath))
        {
            throw new \InvalidArgumentException(sprintf('The path "%s" does not specify a file or directory', $localPath));
        }

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
     * {@inheritdoc}
     */
    public function download($remotePath = '/', $localPath, $mode = 0777)
    {
        $remotePath = $this->stripTrailingSlash($remotePath);

        if(!is_dir($localPath))
        {
            throw new \InvalidArgumentException(sprintf('The path "%s" does not specify a directory', $localPath));
        }
        if(!is_writable($localPath))
        {
            throw new \InvalidArgumentException(sprintf('The directory "%s" is not writable', $localPath));
        }

        $localPath = $this->stripTrailingSlash($localPath);

        if(!$this->getAdapter()->isDirectory($remotePath))
        {
            file_put_contents($localPath . '/' . basename($remotePath), $this->get($remotePath)->getContent());
        }
        else
        {
            $filesystem = new BaseFilesystem(new Adapter\Local($localPath, true, $mode));

            $keys = $this->listKeys($remotePath);
            foreach($keys['keys'] as $key)
            {
                $filesystem->write($key, $this->get($key)->getContent());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove($path)
    {
        return $this->delete($path);
    }

    /**
     * {@inheritdoc}
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
    private function stripSlashes($path)
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

    /**
     * @return LocalFilesystem
     */
    private function getLocalFilesystem()
    {
        return new LocalFilesystem();
    }
}