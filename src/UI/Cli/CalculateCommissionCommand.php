<?php

declare(strict_types=1);

namespace Acme\UI\Cli;

use Acme\Commission\Application\Command\CalculateCommissionFee\CalculateCommissionFeeFromFileCommand;
use Acme\Commission\Application\Command\CalculateCommissionFee\CalculateCommissionFeeFromFileHandler;
use Acme\Service\Math;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class CalculateCommissionCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        $path = dirname(__DIR__, 3).'/data/'.$file;

        try {
            $handler = new CalculateCommissionFeeFromFileHandler();
            $result = $handler->handle(
                new CalculateCommissionFeeFromFileCommand(
                    $path,
                    $input->getArgument('exchange-rate-uri')
                )
            );
        } catch (Throwable) {
            // log
            return Command::FAILURE;
        }

        $output->writeln($result);

        return Command::SUCCESS;
    }
}
