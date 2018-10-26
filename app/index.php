<?php

namespace CreativeServices\Workshop;

use CreativeServices\Workshop\Application\Application;
use CreativeServices\Workshop\Environment\Environment;

if (isset($_ENV['WORKSHOP_AUTOLOADER_PATH'])) {
    require_once($_ENV['WORKSHOP_AUTOLOADER_PATH']);
}

if (isset($_ENV['WORKSHOP_CONFIG_PATH'])) {
    $environment = Environment::create($_ENV['WORKSHOP_CONFIG_PATH']);
} else {
    throw new \DomainException("Configuration missing");
}

(new Application($environment))->run();