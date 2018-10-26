<?php

$vendor = DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
if (false !== $pos = strrpos(__FILE__, $vendor)) {
    $vendorDir = substr(__FILE__, 0, $pos + strlen($vendor));
    $autoloaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';
    if (is_file($autoloaderPath)) require_once($autoloaderPath);
}
