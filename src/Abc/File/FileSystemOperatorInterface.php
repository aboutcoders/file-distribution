<?php

namespace Abc\File;

interface FileSystemOperatorInterface
{
    /**
     * @param $remoteUrl
     * @param $localFilePath
     * @return string
     */
    public function download($remoteUrl, $localFilePath);

    /**
     * @param string $localFilePath
     * @param string $remoteUrl
     * @internal param string $url
     * @return void
     */
    public function upload($localFilePath, $remoteUrl);

    /**
     * @param string $url
     * @return boolean
     */
    public function exists($url);

}