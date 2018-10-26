<?php

namespace CreativeServices\Workshop\Environment;

use CreativeServices\Workshop\Template\TemplateCollectionInterface;
use CreativeServices\Workshop\Template\TemplateInterface;

interface EnvironmentInterface
{
    /**
     * @param string $path
     * @return \SplFileInfo
     */
    public function getAsset($path);

    /**
     * @param string $name
     * @return TemplateInterface
     */
    public function getTemplate($name);

    /**
     * @return TemplateCollectionInterface
     */
    public function getTemplates();

    /**
     * @param string $path
     * @return bool
     */
    public function hasAsset($path);

    /**
     * @param string $name
     * @return bool
     */
    public function hasTemplate($name);

    /**
     * @param string $template
     * @return string
     */
    public function render($template);
}