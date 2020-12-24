<?php

declare(strict_types=1);

namespace Acme\Component\MonolithicConsole\Display;

use Acme\Component\SymfonyMonolith\ApplicationRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Question\Question;
use UnexpectedValueException;

final class ApplicationPicker
{
    public const OPTION_ALL = 'all';

    /**
     * @var ApplicationRegistry
     */
    private $registry;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @var array<string>
     */
    private $pickedApplications;

    /**
     * @var QuestionHelper
     */
    private $helper;

    public function __construct(ApplicationRegistry $registry, InputInterface $input, ConsoleOutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->registry = $registry;
        $this->helper = new QuestionHelper();
        $this->pickedApplications = [];
    }

    /**
     * @return array<string>
     */
    public function ask(): array
    {
        $section = $this->output->section();

        while (count($this->getPickableApplications()) > 0) {
            /** @var string|null $pickedOption */
            $pickedOption = $this->helper->ask($this->input, $section, $this->createQuestion());
            $section->clear();

            if (self::OPTION_ALL === $pickedOption) {
                $this->pickedApplications = $this->registry->getApplicationNames();

                break;
            }

            if (null === $pickedOption) {
                if (0 === count($this->pickedApplications)) {
                    continue;
                }

                break;
            }

            $this->pickedApplications[] = $pickedOption;
        }

        return $this->pickedApplications;
    }

    public function reset(): void
    {
        $this->pickedApplications = [];
    }

    private function createQuestion(): Question
    {
        $question = 'What application would you like to run a command in?';
        if (count($this->pickedApplications) > 0) {
            $question .= sprintf(' (selected: %s)', implode(', ', $this->pickedApplications));
        }

        $options = array_merge([self::OPTION_ALL], $this->getPickableApplications());
        $question .= PHP_EOL . $this->formatOptions($options);

        $q = new Question($question);
        $q->setValidator($this->createValidator($options));

        return $q;
    }

    /**
     * @param array<int, string> $options
     */
    private function formatOptions(array $options): string
    {
        $list = [];
        foreach ($options as $index => $option) {
            $list[] = $this->formatOption($index, $option);
        }

        return implode(PHP_EOL, $list);
    }

    private function formatOption(int $index, string $option): string
    {
        return sprintf(' > [%d] %s', $index, $option);
    }

    /**
     * @param array<string> $options
     */
    private function createValidator(array $options): callable
    {
        return function (?string $input) use ($options): ?string {
            if (isset($options[$input])) {
                return $options[$input];
            }

            if (null === $input || '' === $input) {
                return null;
            }

            if (self::OPTION_ALL === $input) {
                return self::OPTION_ALL;
            }

            if (!in_array($input, $this->getPickableApplications(), true)) {
                throw new UnexpectedValueException('Invalid application.');
            }

            return $input;
        };
    }

    /**
     * @return array<int, string>
     */
    private function getPickableApplications(): array
    {
        return array_values(array_diff($this->registry->getApplicationNames(), $this->pickedApplications));
    }
}
