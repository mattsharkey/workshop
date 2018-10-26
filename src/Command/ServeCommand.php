<?php

namespace CreativeServices\Workshop\Command;

use CreativeServices\Workshop\Server\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class ServeCommand extends Command implements ServeCommandInterface
{
    protected function configure()
    {
        $this
            ->setName('serve')
            ->setDescription("Start a local development server")
            ->addArgument('path', InputArgument::REQUIRED, 'The path to the configuration file')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $callback = null;
        $disableOutput = false;
        if ($output->isQuiet()) {
            $disableOutput = true;
        } else {
            $callback = function ($type, $buffer) use ($output) {
                if (Process::ERR === $type && $output instanceof ConsoleOutputInterface) {
                    $output = $output->getErrorOutput();
                }
                $output->write($buffer, false, OutputInterface::OUTPUT_RAW);
            };
        }

        try {
            $path = $input->getArgument('path');
            $port = $input->getOption('port');
            $server = new Server($path, $port);
            $io->success(sprintf('Server listening on http://%s', $server->getAddress()));
            $io->comment('Quit the server with CONTROL-C.');
            $server->run($disableOutput, $callback);
            return 0;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}