<?php

namespace Abc\File;

use Abc\File\Exception\FilesystemException;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;

class FilesystemFactory
{
    /**
     * @param LocationInterface $location
     * @throws FilesystemException
     * @return Filesystem
     */
    public function buildFilesystem(LocationInterface $location)
    {
        return self::build($location->getType(), $location->getUrl(), $location->getProperties());
    }

    public static function build($type, $path, $options = array())
    {

        switch ($type) {
            case FilesystemType::FTP:
                if (!isset($options['host'])) {
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

        return new Filesystem($adapter);
    }
} 