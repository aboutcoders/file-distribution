<?php

namespace Abc\File;

use Abc\File\Exception\FilesystemException;
use Gaufrette\Filesystem as GaufretteFilesystem;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;

class FilesystemFactory
{
    /**
     * @param FilesystemInterface $filesystem
     * @throws FilesystemException
     * @return GaufretteFilesystem
     */
    public function buildFilesystem(FilesystemInterface $filesystem)
    {
        return self::build($filesystem->getType(), $filesystem->getPath(), $filesystem->getProperties());
    }

    public static function build($type, $path, $options = array())
    {

        switch($type)
        {
            case FilesystemType::FTP:
                if(!isset($options['host']))
                {
                    throw new FilesystemException("Host is not set for FTP adapter");
                }
                $adapter = new FtpAdapter($path, $options['host'], $options);
                break;

            case FilesystemType::Filesystem:
                $create  = isset($options['create']) ? $options['create'] : false;
                $mode    = isset($options['mode']) ? $options['mode'] : 0777;
                $adapter = new LocalAdapter($path, $create, $mode);
                break;

            default:
                throw new FilesystemException(sprintf("Adapter %s does not exist", $type));
        }

        return new GaufretteFilesystem($adapter);
    }
} 