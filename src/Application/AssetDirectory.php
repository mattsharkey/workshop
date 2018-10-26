<?php

namespace CreativeServices\Workshop\Application;

use Eloquent\Pathogen\FileSystem\AbsoluteFileSystemPathInterface;
use Eloquent\Pathogen\FileSystem\FileSystemPath;
use Eloquent\Pathogen\FileSystem\FileSystemPathInterface;
use Eloquent\Pathogen\FileSystem\RelativeFileSystemPathInterface;

class AssetDirectory implements AssetDirectoryInterface
{
    /**
     * @var AbsoluteFileSystemPathInterface
     */
    private $root;

    /**
     * @param $pathString
     * @throws \Exception
     * @return AssetDirectory
     */
    public static function fromString($pathString)
    {
        if (!is_dir($pathString)) {
            throw new \Exception("Not a directory: $pathString");
        }
        $assets = new static();
        $assets->root = FileSystemPath::fromString(realpath($pathString));
        return $assets;
    }

    /**
     * @param string $pathString
     * @return \SplFileInfo
     */
    public function getAsset($pathString)
    {
        if (!$this->hasAsset($pathString)) {
            throw new \OutOfBoundsException("File not found: {$pathString}");
        }
        $path = $this->makeAssetPath($pathString);
        return new \SplFileInfo($path->string());
    }

    /**
     * @param string $pathString
     * @return bool
     */
    public function hasAsset($pathString)
    {
        $path = $this->makeAssetPath($pathString);
        if (!$this->isValidPath($path)) {
            throw new \OutOfBoundsException("Invalid path: {$pathString}");
        }
        return is_file($path->string());
    }

    /**
     * @param FileSystemPathInterface $path
     * @return bool
     */
    private function isValidPath(FileSystemPathInterface $path)
    {
        $rootPath = $this->root->string();
        return substr($path->string(), 0, strlen($rootPath)) === $rootPath;
    }

    /**
     * @param $pathString
     * @return AbsoluteFileSystemPathInterface
     */
    private function makeAssetPath($pathString)
    {
        /** @var RelativeFileSystemPathInterface $relativePath */
        $relativePath = FileSystemPath::fromString($pathString);
        return $this->root->resolve($relativePath);
    }
}