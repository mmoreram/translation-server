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

namespace Mmoreram\TranslationServer\Loader;

use Mmoreram\TranslationServer\Model\Project;

/**
 * Class MetricsLoader.
 */
class MetricsLoader
{
    /**
     * Get total metrics given a Project.
     *
     * The format is that one
     *
     * [
     *      'en' => 267,
     *      'ca' => 120,
     *      'es' => 12,
     * ]
     *
     * @param Project $project   Project
     * @param array   $domains   Domains
     * @param array   $languages Languages
     *
     * @return array Metrics
     */
    public function getTotalMetrics(
        Project $project,
        array $domains = [],
        array $languages = []
    ) {
        $metrics = [];
        $masterLanguage = $project->getMasterLanguage();
        $languages = empty($languages)
            ? $project->getAvailableLanguages()
            : $languages;

        $languages = array_intersect(
            $languages,
            $project->getAvailableLanguages()
        );
        unset($languages[$masterLanguage]);

        $masterKeys = $project->getKeys(
            $domains,
            [$masterLanguage]
        );
        $masterCount = count($masterKeys);
        $metrics[$masterLanguage] = $masterCount;

        foreach ($languages as $language) {
            $languageKeys = $project->getKeys($domains, [$language]);
            $existentLanguageTranslations = array_intersect(
                $languageKeys,
                $masterKeys
            );

            $metrics[$language] = count($existentLanguageTranslations);
        }
        arsort($metrics);

        return $metrics;
    }
}
