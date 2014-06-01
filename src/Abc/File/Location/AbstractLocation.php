<?php
/**
 * Created by PhpStorm.
 * User: wojciechciolko
 * Date: 01.06.2014
 * Time: 10:43
 */

namespace Abc\File\Location;


use Abc\File\LocationInterface;
use Gaufrette\Adapter;
use Gaufrette\Filesystem;

abstract class AbstractLocation implements LocationInterface
{

    /** @var Filesystem */
    protected $filesystem;

    function __construct(Adapter $adapter)
    {
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @return Filesystem
     */
    public function getFileSystem()
    {
        return $this->filesystem;
    }

}