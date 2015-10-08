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

namespace Mmoreram\TranslationServer\Command;

use Exception;
use Mmoreram\TranslationServer\Model\Translation;
use Stichoza\GoogleTranslate\TranslateClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GuessCommand
 */
class GuessCommand extends AddCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('translation:server:guess')
            ->setDescription('Guess missing translations');
    }

    /**
     * Execute command
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
        $project = $this->createProject($input);

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

            $masterTranslation = $randomTranslation->getMasterTranslation();
            $translator = new TranslateClient(
                $masterTranslation->getLanguage(),
                $randomTranslation->getLanguage()
            );
            $translation = $translator->translate(
                $masterTranslation->getValue()
            );

            $this
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Language : '.$randomTranslation->getLanguage()
                )
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Key : '.$randomTranslation->getKey()
                )
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Original : '.$randomTranslation
                        ->getMasterTranslation()
                        ->getValue()
                )
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Translation : '.$translation
                );

            $randomTranslation->setValue($translation);
            $masterStructure = $randomTranslation->getStructure();
            $this->overwriteLastValueFromStructure(
                $masterStructure,
                $translation
            );
            $randomTranslation->setStructure($masterStructure);

            $project
                ->addTranslation($randomTranslation)
                ->save();
            $output->writeln('');
        }

        $this->finishCommand($output);
    }
}
