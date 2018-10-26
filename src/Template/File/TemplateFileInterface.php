<?php

namespace CreativeServices\Workshop\Template\File;

use CreativeServices\Workshop\Template\TemplateInterface;

interface TemplateFileInterface extends TemplateInterface
{
    public function getTime();
}