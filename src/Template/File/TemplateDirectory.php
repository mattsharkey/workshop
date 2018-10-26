<?php

namespace CreativeServices\Workshop\Template\File;

use Eloquent\Pathogen\AbsolutePathInterface;
use Eloquent\Pathogen\RelativePathInterface;
use Eloquent\Pathogen\FileSystem\FileSystemPath;

class TemplateDirectory implements TemplateDirectoryInterface, \OuterIterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var AbsolutePathInterface
     */
    private $path;

    public function __construct($path)
    {
        if (!is_dir($path)) {
            throw new \Exception("Not a directory: $path");
        }
        $this->path = FileSystemPath::fromString(realpath($path));
    }

    public function current()
    {
        $file = $this->getInnerIterator()->current();
        return new TemplateFile($this->getTemplateNameFromFile($file), $file);
    }

    public function getInnerIterator()
    {
        if (!isset($this->iterator)) {
            $this->iterator = $this->makeTemplatesIterator();
        }
        return $this->iterator;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getTemplate($name)
    {
        return new TemplateFile($name, $this->getTemplateFile($name));
    }

    public function getTwigLoader(\Twig_LoaderInterface $existing = null)
    {
        $workshop = new \Twig_Loader_Filesystem();
        $workshop->setPaths([$this->path->string()], '_workshop');
        if ($existing) {
            return new \Twig_Loader_Chain([$existing, $workshop]);
        }
        return $workshop;
    }

    public function hasTemplate($name)
    {
        $path = $this->makeTemplatePath($name);
        return is_file($path->string());
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function next()
    {
        $this->getInnerIterator()->next();
    }

    public function rewind()
    {
        $this->getInnerIterator()->rewind();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }

    private function addTwigLoaderToEnvironment(\Twig_Environment $twig)
    {
        $twig->setLoader($this->getTwigLoader($twig->getLoader()));
    }

    /**
     * @param string $name
     * @return \SplFileInfo
     */
    private function getTemplateFile($name)
    {
        $path = $this->makeTemplatePath($name);
        return new \SplFileInfo($path->string());
    }

    /**
     * @param \SplFileInfo $file
     * @return string
     */
    private function getTemplateNameFromFile(\SplFileInfo $file)
    {
        return $this->makeRelativeTemplatePath($file->getRealPath())->string();
    }

    /**
     * @param string $absolutePath
     * @return RelativePathInterface
     */
    private function makeRelativeTemplatePath($absolutePath)
    {
        /** @var AbsolutePathInterface $templatePath */
        $templatePath = FileSystemPath::fromString($absolutePath);
        return $templatePath->relativeTo($this->path);
    }

    /**
     * @param \Iterator $files
     * @return \RegexIterator
     */
    private function makeTemplateFilter(\Iterator $files)
    {
        return new \RegexIterator($files, '|\.twig$|');
    }

    private function makeTemplatePath($name)
    {
        /** @var RelativePathInterface $relativePath */
        $relativePath = FileSystemPath::fromString($name);
        return $this->path->join($relativePath);
    }

    /**
     * @return \RegexIterator
     */
    private function makeTemplatesIterator()
    {
        $dir = new \RecursiveDirectoryIterator($this->path->string(), \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::LEAVES_ONLY);
        return $this->makeTemplateFilter($files);
    }
}