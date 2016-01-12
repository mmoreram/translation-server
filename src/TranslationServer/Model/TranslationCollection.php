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

namespace Mmoreram\TranslationServer\Model;

use Mmoreram\TranslationServer\Model\Abstracts\TranslationAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class TranslationCollection.
 */
class TranslationCollection
    extends TranslationAccessible
    implements
        Sortable
{
    /**
     * @var Translation[]
     *
     * Translation
     */
    private $translations;

    /**
     * Constructor.
     *
     * @param Translation[] $translations Translations
     */
    private function __construct(array $translations)
    {
        $this->translations = $translations;
    }

    /**
     * Add translation.
     *
     * @param Translation $translation Translation
     *
     * @return $this Self object
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Get translations.
     *
     * @param array    $domains   Domains
     * @param array    $languages Languages
     * @param callable $filter    Filter function
     *
     * @return Translation[] $translations Set of translations
     */
    public function getTranslations(
        array $domains = [],
        array $languages = [],
        callable $filter = null
    ) {
        $translations = array_filter(
            $this->translations,
            function (Translation $translation) {

                return $translation->getValue() != '';
            }
        );

        if (is_callable($filter)) {
            $translations = array_filter(
                $translations,
                $filter
            );
        }

        return $translations;
    }

    /**
     * Save structure.
     *
     * @return $this Self object
     */
    public function sort()
    {
        $this->recursiveSort(
            $this->translations
        );

        return $this;
    }

    /**
     * Sort array level.
     *
     * @param array $element Element to sort
     */
    private function recursiveSort(array &$element)
    {
        foreach ($element as &$value) {
            if (is_array($value)) {
                $this->recursiveSort($value);
            }
        }

        uasort($element, function (
            Translation $a,
            Translation $b
        ) {
            return strcmp($a->getKey(), $b->getKey());
        });
    }

    /**
     * Create new instance of Translation collection.
     *
     * @param Translation[] $translations Translations
     *
     * @return self New instance
     */
    public static function create(array $translations)
    {
        return new self($translations);
    }
}
