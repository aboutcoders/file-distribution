<?php

namespace Abc\File;

use Abc\File\Exception\FilesystemException;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;

class FilesystemFactory
{
    public static function build($type, $path, $options = array())
    {

        switch ($type) {
            case FilesystemType::FTP:
                if (!isset($options['host'])) {
                    throw new FilesystemException("host is not set for FTP adapter");
                }
                $adapter = new FtpAdapter($path, $options['host'], $options);
                break;
            case FilesystemType::Filesystem:
                $create  = isset($options['create']) ? $options['create'] : false;
                $mode    = isset($options['mode']) ? $options['mode'] : 0777;
                $adapter = new LocalAdapter($path, $create, $mode);
                break;
            default:
                throw new FilesystemException("Invalid FileSystem adapter");
        }

        return new Filesystem($adapter);
    }
} 