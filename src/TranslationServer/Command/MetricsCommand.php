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
use Mmoreram\TranslationServer\Command\Abstracts\AbstractTranslationServerCommand;
use Mmoreram\TranslationServer\Loader\MetricsLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MetricsCommand
 */
class MetricsCommand extends AbstractTranslationServerCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('translation:server:view')
            ->setDescription('View statics about the server');

        parent::configure();
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
        $inputDomains = $input->getOption('domain');
        $inputLanguages = $input->getOption('language');

        $project = $this->createProject($input);
        $metricsLoader = new MetricsLoader();
        $metrics = $metricsLoader
            ->getTotalMetrics(
                $project,
                $inputDomains,
                $inputLanguages
            );

        $masterLanguage = $project->getMasterLanguage();
        $masterLanguageKeys = $metrics[$masterLanguage];
        foreach ($metrics as $language => $percentFinished) {
            $languageTranslations = $metrics[$language];
            $languageTranslationsMissing = $masterLanguageKeys - $languageTranslations;
            $languageCompleted = round((100 / $masterLanguageKeys) * $languageTranslations, 2);
            $this
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Translations for ['.$language.'] is '.$languageCompleted.'% completed. '.$languageTranslationsMissing.' missing'
                );
        }

        $this->finishCommand($output);
    }
}
