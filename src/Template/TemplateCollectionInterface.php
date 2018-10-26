<?php

namespace CreativeServices\Workshop\Template;

interface TemplateCollectionInterface extends \Iterator
{
    public function getTemplate($name);

    public function hasTemplate($name);
}