<?php

namespace Abc\Filesystem;

use Gaufrette\Adapter;
use Gaufrette\Adapter\Ftp as FtpAdapter;
use Gaufrette\Adapter\Local as LocalAdapter;

/**
 * Factory class for Gaufrette\Adapter
 */
class AdapterFactory implements AdapterFactoryInterface
{

    /**
     * {@inheritdoc}
     */
    public function create($type, $path, array $options = array())
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