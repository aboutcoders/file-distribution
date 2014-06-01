<?php
/**
 * Created by PhpStorm.
 * User: wojciechciolko
 * Date: 01.06.2014
 * Time: 10:42
 */

namespace Abc\File\Location;


use Abc\File\LocationTypeInterface;
use Gaufrette\Adapter;
use Gaufrette\Adapter\Ftp as FtpAdapter;

class FtpLocation extends AbstractLocation
{
    /**
     * Constructor
     *
     * @param string $directory The directory to use in the ftp server
     * @param string $host      The host of the ftp server
     * @param array  $options   The options like port, username, password, passive, create, mode
     */
    function __construct($directory, $host, $options)
    {
        $adapter   = new FtpAdapter($directory, $host, $options);
        $this->url = $directory;
        parent::__construct($adapter);
    }

    /**
     * @return string
     */
    public function getType()
    {
        // TODO: Implement getType() method.
    }

    /**
     * @param LocationTypeInterface $type
     */
    public function setType(LocationTypeInterface $type)
    {
        // TODO: Implement setType() method.
    }

}