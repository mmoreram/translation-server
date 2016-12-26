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

namespace Mmoreram\TranslationServer\Model;

use Mmoreram\TranslationServer\Model\Abstracts\TranslationAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class TranslationCollection.
 */
class TranslationCollection extends TranslationAccessible implements
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
     * @param Translation[] $translations
     */
    private function __construct(array $translations)
    {
        $this->translations = $translations;
    }

    /**
     * Add translation.
     *
     * @param Translation $translation
     */
    public function addTranslation(Translation $translation)
    {
        $this->translations[] = $translation;
    }

    /**
     * Get translations.
     *
     * @param array    $domains
     * @param array    $languages
     * @param callable $filter
     *
     * @return Translation[]
     */
    public function getTranslations(
        array $domains = [],
        array $languages = [],
        callable $filter = null
    ) : array {
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
     */
    public function sort()
    {
        $this->recursiveSort(
            $this->translations
        );
    }

    /**
     * Sort array level.
     *
     * @param array $element
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
     * @param Translation[] $translations
     *
     * @return TranslationCollection
     */
    public static function create(array $translations)
    {
        return new self($translations);
    }
}
