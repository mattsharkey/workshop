<?php

namespace CreativeServices\Workshop\Template\File;

use CreativeServices\Workshop\Template\TemplateCollectionInterface;
use Eloquent\Pathogen\PathInterface;

interface TemplateDirectoryInterface extends TemplateCollectionInterface
{
    /**
     * @return PathInterface
     */
    public function getPath();

    /**
     * @return \Twig_LoaderInterface
     */
    public function getTwigLoader();
}