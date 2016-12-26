<?php

/*
 * This file is part of the translation-server package
 *
 * Copyright (c) 2015 Marc Morera
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Mmoreram\TranslationServer\Command\Abstracts;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Stopwatch\StopwatchEvent;

use Mmoreram\TranslationServer\Finder\ConfigFinder;
use Mmoreram\TranslationServer\Loader\ConfigLoader;
use Mmoreram\TranslationServer\Model\Project;

/**
 * Class AbstractTranslationServerCommand.
 */
class AbstractTranslationServerCommand extends Command
{
    /**
     * @var Stopwatch
     *
     * Stopwatch instance
     */
    private $stopwatch;

    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->addOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                'Config file directory',
                getcwd()
            )
            ->addOption(
                '--domain',
                '-d',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Desired domains',
                []
            )
            ->addOption(
                '--language',
                '-l',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Desired languages',
                []
            );
    }

    /**
     * Create project given an input instance.
     *
     * @param InputInterface $input
     *
     * @return Project
     */
    public function createProjectByInput(InputInterface $input) : Project
    {
        $configFinder = new ConfigFinder();
        $configLoader = new ConfigLoader();

        /**
         * This section is just for finding the right values to work with in
         * this execution.
         *
         * $options array will have, after this block, all these values
         */
        $configPath = rtrim($input->getOption('config'), DIRECTORY_SEPARATOR);
        $configValues = $configLoader->loadConfigValues(
            $configFinder->findConfigFile($configPath)
        );

        $masterLanguage = $configValues['master_language'];
        $languages = $configValues['languages'];
        $filePaths = $configValues['paths'];

        return Project::create(
            $masterLanguage,
            $languages,
            $filePaths
        );
    }

    /**
     * Start command.
     *
     * @param OutputInterface $output
     * @param bool            $longCommand
     */
    protected function startCommand(
        OutputInterface $output,
        bool $longCommand = false
    ) {
        $this->configureFormatter($output);
        $this->stopwatch = new Stopwatch();
        $this->startStopWatch('command');
        $output->writeln('');
        $this
            ->printMessage(
                $output,
                $this->getProjectHeader(),
                'Command started at ' . date('r')
            );

        if ($longCommand) {
            $this
                ->printMessage(
                    $output,
                    $this->getProjectHeader(),
                    'This process may take a few minutes. Please, be patient'
                );
        }
    }

    /**
     * Configure formatter with Elcodi specific style.
     *
     * @param OutputInterface $output
     */
    protected function configureFormatter(OutputInterface $output)
    {
        $formatter = $output->getFormatter();
        $formatter->setStyle('header', new OutputFormatterStyle('green'));
        $formatter->setStyle('failheader', new OutputFormatterStyle('red'));
        $formatter->setStyle('body', new OutputFormatterStyle('white'));
    }

    /**
     * Start stopwatch.
     *
     * @param string $eventName
     *
     * @return StopwatchEvent
     */
    protected function startStopWatch($eventName) : StopwatchEvent
    {
        return $this
            ->stopwatch
            ->start($eventName);
    }

    /**
     * Print message.
     *
     * @param OutputInterface $output
     * @param string          $header
     * @param string          $body
     */
    protected function printMessage(
        OutputInterface $output,
        $header,
        $body
    ) {
        $message = sprintf(
            '<header>%s</header> <body>%s</body>',
            '[' . $header . ']',
            $body
        );
        $output->writeln($message);
    }

    /**
     * Print message.
     *
     * @param OutputInterface $output
     * @param string          $header
     * @param string          $body
     */
    protected function printMessageFail(
        OutputInterface $output,
        string $header,
        string $body
    ) {
        $message = sprintf(
            '<failheader>%s</failheader> <body>%s</body>',
            '[' . $header . ']',
            $body
        );
        $output->writeln($message);
    }

    /**
     * Finish command.
     *
     * @param OutputInterface $output
     */
    protected function finishCommand(OutputInterface $output)
    {
        $event = $this->stopStopWatch('command');
        $this
            ->printMessage(
                $output,
                $this->getProjectHeader(),
                'Command finished in ' . $event->getDuration() . ' milliseconds'
            );
        $this->printMessage(
                $output,
                $this->getProjectHeader(),
                'Max memory used: ' . $event->getMemory() . ' bytes'
            );
        $output->writeln('');
    }

    /**
     * Stop stopwatch.
     *
     * @param string $eventName Event name
     *
     * @return StopwatchEvent
     */
    protected function stopStopWatch($eventName) : StopwatchEvent
    {
        return $this
            ->stopwatch
            ->stop($eventName);
    }

    /**
     * Get project header.
     *
     * @return string
     */
    protected function getProjectHeader() : string
    {
        return 'Trans Server';
    }
}
