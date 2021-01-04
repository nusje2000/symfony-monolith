<?php

declare(strict_types=1);

namespace Acme\Component\MonolithicConsole;

use Acme\Component\MonolithicConsole\Display\ApplicationPicker;
use Acme\Component\SymfonyMonolith\Loader\ApplicationRegistry;
use Nusje2000\ProcessRunner\Executor\ExecutorInterface;
use Nusje2000\ProcessRunner\Executor\ParallelExecutor;
use Nusje2000\ProcessRunner\Listener\ConsoleListener;
use Nusje2000\ProcessRunner\Listener\StaticConsoleListener;
use Nusje2000\ProcessRunner\Task;
use Nusje2000\ProcessRunner\TaskList;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use UnexpectedValueException;

final class Application
{
    /**
     * @var ApplicationRegistry
     */
    private $applicationRegistry;

    /**
     * @var string
     */
    private $consoleBinary;

    public function __construct(ApplicationRegistry $applicationRegistry, string $consoleBinary = 'php bin/console')
    {
        $this->applicationRegistry = $applicationRegistry;
        $this->consoleBinary = $consoleBinary;
    }

    /**
     * @psalm-return no-return
     */
    public function run(InputInterface $input, OutputInterface $output): void
    {
        $this->prepareInput($input);
        if (!$output instanceof ConsoleOutputInterface) {
            throw new UnexpectedValueException(sprintf('Expected output to be an instance of "%s".', ConsoleOutputInterface::class));
        }

        $applications = $this->applicationPicker($input, $output);
        $return = $this->execute($input, $output, $applications);

        exit($return);
    }

    private function prepareInput(InputInterface $input): void
    {
        if ($input->hasOption('n') || $input->hasOption('no-interaction')) {
            $input->setInteractive(false);
        }
    }

    /**
     * @return array<string>
     */
    private function applicationPicker(InputInterface $input, ConsoleOutputInterface $output): array
    {
        /** @var mixed $option */
        $option = $input->getParameterOption(['--application', '-a'], null);

        if ($option === ApplicationPicker::OPTION_ALL) {
            return $this->applicationRegistry->getApplicationNames();
        }

        if (is_string($option)) {
            return explode(',', $option);
        }

        $picker = new ApplicationPicker($this->applicationRegistry, $input, $output);

        return $picker->ask();
    }

    /**
     * @param array<string> $applications
     */
    private function execute(InputInterface $input, ConsoleOutputInterface $output, array $applications): int
    {
        $output->writeln(sprintf('Executing "%s" on applications: %s', $this->getConsoleCommand(), implode(', ', $applications)));

        $taskList = $this->createTaskList($applications);
        $this->createExecutor($input, $output)->execute($taskList);

        if ($taskList->getFailedTasks()->count() > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * @param array<string> $applications
     */
    private function createTaskList(array $applications): TaskList
    {
        return new TaskList(
            array_map(function (string $application): Task {
                return $this->createTask($application);
            }, $applications)
        );
    }

    private function createTask(string $application): Task
    {
        return new Task($application, Process::fromShellCommandline(
            $this->prepareApplicationCommand(
                $this->getConsoleCommand(),
                $application
            )
        ));
    }

    private function createExecutor(InputInterface $input, ConsoleOutputInterface $output): ExecutorInterface
    {
        $listener = new ConsoleListener($output);
        if (!$input->isInteractive()) {
            $listener = new StaticConsoleListener($output);
        }

        $executor = new ParallelExecutor();
        $executor->addListener($listener);

        return $executor;
    }

    private function prepareApplicationCommand(string $command, string $application): string
    {
        return sprintf('%s --application %s', $command, $application);
    }

    private function getConsoleCommand(): string
    {
        /** @var array<string> $argv */
        $argv = $_SERVER['argv'];
        $argv[0] = $this->consoleBinary;

        $command = implode(' ', $argv);

        // Clear application parameters (--application=some_app, -a some_app, etc.)
        $command = (string) preg_replace('/(\s)?(--application|-a)(\s|=)([\w_,]+)/i', '', $command);

        return $command;
    }
}
