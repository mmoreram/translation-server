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

use Exception;
use Symfony\Component\Finder\Finder;

use Mmoreram\TranslationServer\Model\Abstracts\RepositoryAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Saveable;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class Project.
 */
class Project extends RepositoryAccessible implements
        Sortable,
        Saveable
{
    /**
     * @var string
     *
     * Master language
     */
    private $masterLanguage;

    /**
     * @var string[]
     *
     * Available languages
     */
    private $availableLanguages;

    /**
     * @var string[]
     *
     * Paths
     */
    private $paths;

    /**
     * @var RepositoryCollection
     *
     * Repository collection
     */
    private $repositoryCollection;

    /**
     * Construct.
     *
     * @param RepositoryCollection $repositoryCollection
     * @param string               $masterLanguage
     * @param string[]             $availableLanguages
     * @param string[]             $paths
     */
    private function __construct(
        RepositoryCollection $repositoryCollection,
        string $masterLanguage,
        array $availableLanguages,
        array $paths
    ) {
        $this->repositoryCollection = $repositoryCollection;
        $this->masterLanguage = $masterLanguage;
        $this->availableLanguages = $availableLanguages;
        $this->paths = $paths;
    }

    /**
     * Get MasterLanguage.
     *
     * @return string
     */
    public function getMasterLanguage() : string
    {
        return $this->masterLanguage;
    }

    /**
     * Get AvailableLanguages.
     *
     * @return string[]
     */
    public function getAvailableLanguages() : array
    {
        return $this->availableLanguages;
    }

    /**
     * Get Paths.
     *
     * @return string[]
     */
    public function getPaths() : array
    {
        return $this->paths;
    }

    /**
     * Get Repositories.
     *
     * @param array $domains
     * @param array $languages
     *
     * @return Repository[]
     */
    public function getRepositories(
        array $domains = [],
        array $languages = []
    ) : array {
        return $this
            ->repositoryCollection
            ->getRepositories(
                $domains,
                $languages
            );
    }

    /**
     * Get Translations.
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
            ->repositoryCollection
            ->getTranslations(
                $domains,
                $languages,
                $filter
            );
    }

    /**
     * Get Translation given the language and the key.
     *
     * @param string $language
     * @param string $key
     *
     * @return Translation
     */
    public function getTranslation(
        string $language,
        string $key
    ) : Translation {
        $languages = $this
            ->repositoryCollection
            ->getTranslations(
                [],
                [$language],
                function (Translation $translation) use ($key) {
                    return $translation->getKey() === $key;
                }
            );

        $firstLanguage = reset($languages);

        return $firstLanguage;
    }

    /**
     * Get random Translation. If no translations are available, return null.
     *
     * @param array $domains
     * @param array $languages
     *
     * @return Translation|null
     *
     * @throws Exception You cannot search missing translations by master language
     */
    public function getRandomMissingTranslation(
        array $domains = [],
        array $languages = []
    ) : ? Translation {
        if (in_array($this->masterLanguage, $languages)) {
            throw new Exception('You cannot search by master language missing translations');
        }

        $candidates = [];
        $masterKeys = $this->getKeys(
            $domains,
            [$this->masterLanguage]
        );

        $languages = $languages ?: $this->getAvailableLanguages();

        foreach ($languages as $language) {
            $languageKeys = $this->getKeys(
                $domains,
                [$language]
            );

            $missingKeys = array_diff(
                $masterKeys,
                $languageKeys
            );
            foreach ($missingKeys as $missingKey) {
                $candidates[] = [
                    'language' => $language,
                    'key' => $missingKey,
                ];
            }
        }

        if (empty($candidates)) {
            return null;
        }

        $candidateKey = array_rand($candidates);
        $candidate = $candidates[$candidateKey];
        $masterCandidate = $this->getTranslation(
            $this->masterLanguage,
            $candidate['key']
        );

        $candidateTranslation = Translation::create(
            $candidate['key'],
            '',
            $candidate['language']
        );

        $candidateTranslation->setStructure($masterCandidate->getStructure());
        $candidateTranslation->setMasterTranslation($masterCandidate);

        return $candidateTranslation;
    }

    /**
     * Add translation.
     *
     * @param Translation $translation
     *
     * @throws Exception Inserting a master translation is not allowed
     */
    public function addTranslation(Translation $translation)
    {
        if ($translation->getLanguage() === $this->masterLanguage) {
            throw new Exception('You cannot insert a new translation for master language');
        }

        $originalRepository = $translation
            ->getMasterTranslation()
            ->getRepository();

        $newRepositoryDirname = $originalRepository->getDirname();
        $newRepositoryDomain = $originalRepository->getDomain();
        $newRepositoryLanguage = $translation->getLanguage();

        /**
         * We check if this repository exists. If not, we create it.
         * Otherwise, we use existing one.
         */
        $expectedRepositories = array_filter(
            $this
                ->repositoryCollection
                ->getRepositories(),
            function (Repository $repository) use ($newRepositoryLanguage, $newRepositoryDomain, $newRepositoryDirname) {
                return
                    $repository->getDomain() === $newRepositoryDomain &&
                    $repository->getLanguage() === $newRepositoryLanguage &&
                    $repository->getDirname() === $newRepositoryDirname;
            }
        );

        $expectedRepository = reset($expectedRepositories);

        if (!($expectedRepository instanceof Repository)) {
            $newRepositoryPath = Repository::buildRepositoryPath(
                $newRepositoryDirname,
                $newRepositoryDomain,
                $newRepositoryLanguage
            );
            $expectedRepository = Repository::createEmptyByFilePath($newRepositoryPath);
            $this
                ->repositoryCollection
                ->addRepository($expectedRepository);
        }

        $expectedRepository->addTranslation($translation);
    }

    /**
     * Save structure.
     */
    public function sort()
    {
        $this
            ->repositoryCollection
            ->sort();
    }

    /**
     * Save structure.
     */
    public function save()
    {
        $this
            ->repositoryCollection
            ->save();
    }

    /**
     * Create a project from a list of paths.
     *
     * @param string   $masterLanguage
     * @param string[] $availableLanguages
     * @param string[] $paths
     *
     * @return Project
     */
    public static function create(
        $masterLanguage,
        array $availableLanguages,
        array $paths
    ) : Project {
        $finder = new Finder();
        $finder
            ->files()
            ->in($paths)
            ->name('*.yml')
            ->name('*.yaml');

        $repositories = [];
        foreach ($finder as $file) {
            $filepath = $file->getRealpath();
            $repositories[] = Repository::createByFilePath($filepath);
        }
        $repositoryCollection = RepositoryCollection::create($repositories);

        return new self(
            $repositoryCollection,
            $masterLanguage,
            $availableLanguages,
            $paths
        );
    }
}
