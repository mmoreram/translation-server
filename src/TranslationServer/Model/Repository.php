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

use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Parser as YamlParser;

use Mmoreram\TranslationServer\Model\Abstracts\TranslationAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Saveable;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class Repository.
 */
class Repository extends TranslationAccessible implements
    Sortable,
    Saveable
{
    /**
     * @var string
     *
     * Path
     */
    private $path;

    /**
     * @var string
     *
     * Language
     */
    private $language;

    /**
     * @var string
     *
     * Domain
     */
    private $domain;

    /**
     * @var TranslationCollection
     *
     * Translation collection
     */
    private $translationCollection;

    /**
     * Construct.
     *
     * @param TranslationCollection $translationCollection
     * @param string                $path
     * @param string                $language
     * @param string                $domain
     */
    private function __construct(
        TranslationCollection $translationCollection,
        string $path,
        string $language,
        string $domain
    ) {
        $this->translationCollection = $translationCollection;
        $this->path = $path;
        $this->language = $language;
        $this->domain = $domain;

        foreach ($this
                     ->translationCollection
                     ->getTranslations() as $translation) {
            $translation->setRepository($this);
        }
    }

    /**
     * Create a new repository given a file path.
     *
     * @param string $filepath
     *
     * @return Repository
     */
    public static function createByFilePath($filepath) : Repository
    {
        $filename = basename($filepath);
        list($domain, $language, $extension) = explode('.', $filename, 3);

        $yamlParser = new YamlParser();
        $data = $yamlParser->parse(file_get_contents($filepath));

        $emptyArray = [];
        $translations = [];
        self::createPlainRepresentationByArray(
            $translations,
            $language,
            ($data) ?: [],
            $emptyArray,
            ''
        );

        $translationCollection = TranslationCollection::create($translations);

        return new self(
            $translationCollection,
            $filepath,
            $language,
            $domain
        );
    }

    /**
     * Create empty repository given it's path.
     *
     * @param string $filepath
     *
     * @return Repository
     */
    public static function createEmptyByFilePath(string $filepath) : Repository
    {
        $filename = basename($filepath);
        list($domain, $language, $extension) = explode('.', $filename, 3);

        return new self(
            TranslationCollection::create([]),
            $filepath,
            $language,
            $domain
        );
    }

    /**
     * Given an array, return a list of plain keys and values.
     *
     * @param array  $translations
     * @param string $language
     * @param array  $data
     * @param array  $structure
     * @param string $prefix
     */
    private static function createPlainRepresentationByArray(
        array &$translations,
        string $language,
        array $data,
        array $structure,
        string $prefix
    ) {
        foreach ($data as $key => $value) {
            $currentStructure = $structure;
            if (is_array($value)) {
                $emptyArray = [];
                self::appendValueIntoStructure(
                    $currentStructure,
                    $key,
                    $emptyArray
                );
                self::createPlainRepresentationByArray(
                    $translations,
                    $language,
                    $value,
                    $currentStructure,
                    $prefix . '.' . $key
                );
            } else {
                $builtKey = trim($prefix . '.' . $key, '.');
                $translation = Translation::create(
                    $builtKey,
                    $value,
                    $language
                );

                self::appendValueIntoStructure(
                    $currentStructure,
                    $key,
                    $value
                );
                $translation->setStructure($currentStructure);
                $translations[] = $translation;
            }
        }
    }

    /**
     * Add an element at the end of an array recursively (last child).
     *
     * @param array      $structure
     * @param string|int $key
     * @param mixed      $value
     */
    private static function appendValueIntoStructure(
        array &$structure,
        $key,
        $value
    ) {
        $pointer = &$structure;
        while (!empty($pointer)) {
            $currentKey = key($pointer);
            $pointer = &$pointer[$currentKey];
        }
        $pointer[$key] = $value;
    }

    /**
     * Add translation.
     *
     * @param Translation $translation
     */
    public function addTranslation(Translation $translation)
    {
        $translation->setRepository($this);
        $this
            ->translationCollection
            ->addTranslation($translation);
    }

    /**
     * Get Path.
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Get Path.
     *
     * @return string
     */
    public function buildPath() : string
    {
        return self::buildRepositoryPath(
            dirname($this->path),
            $this->domain,
            $this->language
        );
    }

    /**
     * Get Dirname.
     *
     * @return string
     */
    public function getDirname() : string
    {
        return dirname($this->path);
    }

    /**
     * Get Language.
     *
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * Get Domain.
     *
     * @return string
     */
    public function getDomain() : string
    {
        return $this->domain;
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
        return $this
            ->translationCollection
            ->getTranslations(
                $domains,
                $languages,
                $filter
            );
    }

    /**
     * Save structure.
     */
    public function sort()
    {
        $this
            ->translationCollection
            ->sort();
    }

    /**
     * Save structure.
     */
    public function save()
    {
        $translationStructure = [];
        foreach ($this->getTranslations() as $translation) {
            $translationStructure = array_merge_recursive(
                $translationStructure,
                $translation->getStructure()
            );
        }

        $dumper = new YamlDumper();
        $yaml = $dumper->dump($translationStructure, 1000);

        file_put_contents(
            $this->buildPath(),
            $yaml
        );
    }

    /**
     * Build the repository path given the basename, the domain and the language.
     *
     * @param string $basename
     * @param string $domain
     * @param string $language
     *
     * @return string
     */
    public static function buildRepositoryPath(
        string $basename,
        string $domain,
        string $language
    ) : string {
        return sprintf('%s/%s.%s.yml',
            $basename,
            $domain,
            $language
        );
    }
}
