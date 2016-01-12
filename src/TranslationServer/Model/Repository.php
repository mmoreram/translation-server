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

use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Parser as YamlParser;

use Mmoreram\TranslationServer\Model\Abstracts\TranslationAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Saveable;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class Repository.
 */
class Repository
    extends TranslationAccessible
    implements
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
     * @param TranslationCollection $translationCollection Translation collection
     * @param string                $path                  Path
     * @param string                $language              Language
     * @param string                $domain                Domain
     */
    private function __construct(
        TranslationCollection $translationCollection,
        $path,
        $language,
        $domain
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
     * @param string $filepath File path
     *
     * @return self New Repository instance
     */
    public static function createByFilePath($filepath)
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
     * @param string $filepath File path
     *
     * @return self New Repository instance
     */
    public static function createEmptyByFilePath($filepath)
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
     * @param array  $translations Translations
     * @param string $language     Language
     * @param array  $data         Data
     * @param array  $structure    Structure
     * @param string $prefix       Prefix value
     */
    private static function createPlainRepresentationByArray(
        array &$translations,
        $language,
        array $data,
        array $structure,
        $prefix
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
     * @param array  $structure Structure
     * @param string $key       Key
     * @param mixed  $value     Value
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
        };
        $pointer[$key] = $value;
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
        $translation->setRepository($this);
        $this
            ->translationCollection
            ->addTranslation($translation);
    }

    /**
     * Get Path.
     *
     * @return string Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get Path.
     *
     * @return string Path
     */
    public function buildPath()
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
     * @return string Dirname
     */
    public function getDirname()
    {
        return dirname($this->path);
    }

    /**
     * Get Language.
     *
     * @return string Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get Domain.
     *
     * @return string Domain
     */
    public function getDomain()
    {
        return $this->domain;
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
     *
     * @return $this Self object
     */
    public function sort()
    {
        $this
            ->translationCollection
            ->sort();

        return $this;
    }

    /**
     * Save structure.
     *
     * @return $this Self object
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

        return $this;
    }

    /**
     * Build the repository path given the basename, the domain and the language.
     *
     * @param string $basename Base name
     * @param string $domain   Domain
     * @param string $language Language
     *
     * @return string Repository path
     */
    public static function buildRepositoryPath(
        $basename,
        $domain,
        $language
    ) {
        return sprintf('%s/%s.%s.yml',
            $basename,
            $domain,
            $language
        );
    }
}
