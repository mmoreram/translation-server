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

namespace Mmoreram\TranslationServer;

use Mmoreram\TranslationServer\Model\Project;
use Mmoreram\TranslationServer\Model\Repository;
use Mmoreram\TranslationServer\Model\Translation;

/**
 * Class TranslationPicker.
 */
class TranslationPicker
{
    /**
     * Given a Project, a set of languages and a set of domains, return a random
     * non filled yet Translation object.
     *
     * If the flag revision is enabled, then only already translated elements
     * will be picked up
     *
     * @param Project $project   Project
     * @param array   $languages Languages
     * @param array   $domains   Domains
     * @param bool    $revision  Revision only
     *
     * @return Translation|false Translation found or null if none translation
     *                           is available
     */
    public function pickUpTranslation(
        Project $project,
        array $languages,
        array $domains,
        $revision
    ) {
        $repositories = $project
            ->getRepositories(
                $domains,
                $languages
            );

        $translations = array_reduce(
            $repositories,
            function (Repository $repository) use ($revision) {

                $translations = $repository->getTranslations();

                return array_filter(
                    $translations,
                    function (Translation $translation) use ($revision) {
                        return $revision === ($translation->getValue() == '');
                    }
                );
            }
        );

        $position = array_rand($translations);

        return $translations[$position];
    }
}
