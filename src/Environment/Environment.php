<?php

namespace CreativeServices\Workshop\Environment;

use CreativeServices\Workshop\Application\AssetDirectory;
use CreativeServices\Workshop\Application\AssetDirectoryInterface;
use CreativeServices\Workshop\Template\TemplateCollectionInterface;

class Environment implements EnvironmentInterface
{
    /**
     * @var AssetDirectoryInterface[]
     */
    private $assets = [];

    /**
     * @var TemplateCollectionInterface
     */
    private $templates;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public static function create($path)
    {
        return (new static())->configure($path);
    }

    public function addAlias($alias, $path)
    {
        $this->assets[$alias] = AssetDirectory::fromString($path);
    }

    public function configure($path) {
        if (!is_file($path)) {
            throw new \Exception("Invalid configuration path: $path");
        }
        $env = include($path);
        return $env instanceof EnvironmentInterface ? $env : $this;
    }

    public function getAsset($path)
    {
        foreach ($this->assets as $alias => $assets) {
            $prefix = rtrim($alias, '/') . '/';
            if ($prefix === substr($path, 0, strlen($prefix))) {
                $path = substr($path, strlen($prefix));
                return $assets->getAsset($path);
            }
        }
        throw new \OutOfBoundsException("Unknown file: $path");
    }

    public function getTemplate($name)
    {
        return $this->templates->getTemplate($name);
    }

    public function getTemplates()
    {
        return $this->templates;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function hasAsset($path)
    {
        foreach ($this->assets as $alias => $assets) {
            $prefix = rtrim($alias, '/') . '/';
            if ($prefix === substr($path, 0, strlen($prefix))) {
                $path = substr($path, strlen($prefix));
                return $assets->hasAsset($path);
            }
        }
        return false;
    }

    public function hasTemplate($name)
    {
        return $this->templates->hasTemplate($name);
    }

    public function render($template, array $context = [])
    {
        return $this->twig->render($template, $context);
    }

    public function setTemplates(TemplateCollectionInterface $templates)
    {
        $this->templates = $templates;
    }

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}