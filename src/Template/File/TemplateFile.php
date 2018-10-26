<?php

namespace CreativeServices\Workshop\Template\File;

class TemplateFile implements TemplateFileInterface
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var string
     */
    private $name;

    public function __construct($name, \SplFileInfo $file)
    {
        $this->name = $name;
        $this->file = $file;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTime()
    {
        return $this->file->getMTime();
    }
}