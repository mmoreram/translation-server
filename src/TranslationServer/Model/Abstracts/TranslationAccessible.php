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

namespace Mmoreram\TranslationServer\Model\Abstracts;

use Mmoreram\TranslationServer\Model\Translation;

/**
 * Class TranslationAccessible.
 */
abstract class TranslationAccessible
{
    /**
     * Get translations.
     *
     * @param array    $domains   Domains
     * @param array    $languages Languages
     * @param callable $filter    Filter function
     *
     * @return Translation[] $translations Set of translations
     */
    abstract public function getTranslations(
        array $domains = [],
        array $languages = [],
        callable $filter = null
    );

    /**
     * Get available keys.
     *
     * @param array $domains   Domains
     * @param array $languages Languages
     *
     * @return array Keys
     */
    public function getKeys(
        array $domains = [],
        array $languages = []
    ) {
        return array_reduce(
            $this->getTranslations(
                $domains,
                $languages
            ),
            function (array $keys, Translation $translation) {
                $keys[] = $translation->getKey();

                return $keys;
            },
            []
        );
    }
}
