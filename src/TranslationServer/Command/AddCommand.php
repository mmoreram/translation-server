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

namespace Mmoreram\TranslationServer\Command;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use Mmoreram\TranslationServer\Command\Abstracts\AbstractTranslationServerCommand;
use Mmoreram\TranslationServer\Model\Translation;

/**
 * Class AddCommand.
 */
class AddCommand extends AbstractTranslationServerCommand
{
    /**
     * configure.
     */
    protected function configure()
    {
        $this
            ->setName('translation:server:add')
            ->setDescription('Add new translation');

        parent::configure();
    }

    /**
     * Execute command.
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startCommand($output, false);
        $domains = $input->getOption('domain');
        $languages = $input->getOption('language');
        $project = $this->createProjectByInput($input);
        $questionHelper = $this->getHelper('question');

        while (true) {
            $randomTranslation = $project->getRandomMissingTranslation(
                $domains,
                $languages
            );

            if (!($randomTranslation instanceof Translation)) {
                $this
                    ->printMessage(
                        $output,
                        'Trans Server',
                        'No more translations for you!'
                    );

                break;
            }

            $this->printMessage(
                $output,
                'Trans Server',
                'Language : ' . $randomTranslation->getLanguage()
            );
            $this->printMessage(
                    $output,
                    'Trans Server',
                    'Key : ' . $randomTranslation->getKey()
                );
            $this->printMessage(
                    $output,
                    'Trans Server',
                    'Original : ' . $randomTranslation
                        ->getMasterTranslation()
                        ->getValue()
                );

            $question = new Question('[Trans Server] Translation : ');
            $translationValue = $questionHelper->ask(
                $input,
                $output,
                $question
            );

            if (false === $translationValue) {
                break;
            }

            $randomTranslation->setValue($translationValue);
            $masterStructure = $randomTranslation->getStructure();
            $this->overwriteLastValueFromStructure(
                $masterStructure,
                $translationValue
            );
            $randomTranslation->setStructure($masterStructure);

            $project->addTranslation($randomTranslation);
            $project->save();
            $output->writeln('');
        }

        $this->finishCommand($output);
    }

    /**
     * Overwrite the last child of a structure for given value.
     *
     * @param array $structure
     * @param mixed $value
     */
    protected function overwriteLastValueFromStructure(
        array &$structure,
        $value
    ) {
        $pointer = &$structure;
        $currentKey = key($pointer);
        $currentValue = &$pointer[$currentKey];
        while (is_array($currentValue)) {
            $pointer = &$currentValue;
            $currentKey = key($pointer);
            $currentValue = &$pointer[$currentKey];
        }

        $pointer[$currentKey] = $value;
    }
}
