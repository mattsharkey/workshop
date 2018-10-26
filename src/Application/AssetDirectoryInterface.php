<?php

namespace CreativeServices\Workshop\Application;

interface AssetDirectoryInterface
{
    /**
     * @param string $path
     * @return \SplFileInfo
     */
    public function getAsset($path);

    /**
     * @param string $path
     * @return bool
     */
    public function hasAsset($path);
}
