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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Mmoreram\TranslationServer\Command\Abstracts\AbstractTranslationServerCommand;
use Mmoreram\TranslationServer\Loader\MetricsLoader;

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
            ->setDescription('View statics about the server')
            ->addOption(
                '--export',
                '-e',
                InputOption::VALUE_OPTIONAL,
                "Export missing keys",
                null
            )
        ;

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
            $languageCompleted = ($masterLanguageKeys) ? round((100 / $masterLanguageKeys) * $languageTranslations, 2) : 0;
            $this
                ->printMessage(
                    $output,
                    'Trans Server',
                    'Translations for ['.$language.'] is '.$languageCompleted.'% completed. '.$languageTranslationsMissing.' missing'
                );

            if ($input->getOption('export')) {
                $this->dumpFiles($metricsLoader->getMissingTranslationsPerLanguage($language), $language);
            }

        }

        $this->finishCommand($output);
    }

    /**
     * @param  array  $missingTranslationsPerLanguage
     * @param  string $language
     */
    protected function dumpFiles($missingTranslationsPerLanguage, $language)
    {
        file_put_contents(json_encode($missingTranslationsPerLanguage), 'missing.' . $language);
    }
}
