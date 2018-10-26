<?php

namespace CreativeServices\Workshop\Server;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class Server implements ServerInterface
{
    private $path;

    private $port;

    public function __construct($path, $port = null)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("Invalid configuration path: $path");
        }
        $this->path = $path;
        $this->port = $port;
    }

    public function getAddress()
    {
        return '127.0.0.1:' . $this->getPort();
    }

    public function getPath()
    {
        return realpath($this->path);
    }

    public function getPort()
    {
        return $this->port ?: '8000';
    }

    public function run($disableOutput = true, callable $callback = null)
    {
        $process = $this->createServerProcess();
        if ($disableOutput) {
            $process->disableOutput();
            $callback = null;
        } else {
            try {
                $process->setTty(true);
                $callback = null;
            } catch (RuntimeException $e) {
            }
        }

        $process->run($callback);

        if (!$process->isSuccessful()) {
            $error = 'Server terminated unexpectedly.';
            if ($process->isOutputDisabled()) {
                $error .= ' Run the command again with -v option for more details.';
            }

            throw new \RuntimeException($error);
        }
    }

    private function createServerProcess()
    {
        $finder = new PhpExecutableFinder();
        if (false === $binary = $finder->find(false)) {
            throw new \RuntimeException('Unable to find the PHP binary.');
        }
        $command = array_merge(
            [$binary],
            $finder->findArguments(),
            ['-dvariables_order=EGPCS', '-S', $this->getAddress(), $this->getRouterPath()]
        );
        $env = [
            'WORKSHOP_AUTOLOADER_PATH' => $this->getAutoloaderPath(),
            'WORKSHOP_CONFIG_PATH' => $this->getPath(),
        ];
        $process = new Process($command);
        $process->setWorkingDirectory($this->getDocumentRoot());
        $process->setTimeout(null);
        $process->setEnv($env);
        $process->inheritEnvironmentVariables();
        return $process;
    }

    private function getAutoloaderPath()
    {
        $autoloaderClass = new \ReflectionClass(ClassLoader::class);
        $vendorDirectory = dirname(dirname($autoloaderClass->getFileName()));
        $autoloaderPath = $vendorDirectory . DIRECTORY_SEPARATOR . 'autoload.php';
        if (!is_file($autoloaderPath)) {
            throw new \RuntimeException("Failed to find the autoloader script");
        }
        return $autoloaderPath;
    }

    private function getDocumentRoot()
    {
        return dirname(realpath($this->path));
    }

    private function getRouterPath()
    {
        $router = __DIR__ . '/../../app/index.php';
        if (!is_file($router)) {
            throw new \RuntimeException("Router script not found");
        }
        return realpath($router);
    }
}