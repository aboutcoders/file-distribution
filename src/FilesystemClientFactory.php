<?php

namespace Abc\File;

use Gaufrette\Adapter;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;

class FilesystemClientFactory
{
    /**
     * @param FilesystemInterface $filesystem
     * @return \Abc\File\FilesystemClient
     * @throws \InvalidArgumentException
     */
    public function create(FilesystemInterface $filesystem)
    {
        $adapter = $this->createAdapter($filesystem->getType(), $filesystem->getPath(), $filesystem->getProperties());

        return new FilesystemClient($adapter);
    }

    /**
     * @param       $type
     * @param       $path
     * @param array $options
     * @return \Gaufrette\Adapter
     * @throws \InvalidArgumentException
     */
    public function createAdapter($type, $path, array $options = array())
    {
        switch($type)
        {
            case FilesystemType::FTP:
                if(!isset($options['host']))
                {
                    throw new \InvalidArgumentException('The host must be configured in $options');
                }

                return new FtpAdapter($path, $options['host'], $options);

            case FilesystemType::LOCAL:
                $create = isset($options['create']) ? $options['create'] : false;
                $mode   = isset($options['mode']) ? $options['mode'] : 0777;

                return new LocalAdapter($path, $create, $mode);

            default:
                throw new \InvalidArgumentException(sprintf('The type "%s" is not supported', $type));
        }
    }
}