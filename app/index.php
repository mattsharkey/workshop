<?php

namespace CreativeServices\Workshop;

use CreativeServices\Workshop\Application\Application;
use CreativeServices\Workshop\Environment\Environment;

require_once __DIR__ . '/../autoload.php';

if (isset($_ENV['WORKSHOP_CONFIG_PATH'])) {
    $environment = Environment::create($_ENV['WORKSHOP_CONFIG_PATH']);
} else {
    throw new \DomainException("Configuration missing");
}

(new Application($environment))->run();