<?php

namespace Abc\Filesystem;

use MyCLabs\Enum\Enum;

/**
 * FilesystemType
 *
 * @method static FilesystemType FTP()
 * @method static FilesystemType LOCAL()
 */
class FilesystemType extends Enum
{
    const FTP   = 'FTP';
    const LOCAL = 'LOCAL';
}