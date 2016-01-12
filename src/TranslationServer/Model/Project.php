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

use Exception;
use Symfony\Component\Finder\Finder;

use Mmoreram\TranslationServer\Model\Abstracts\RepositoryAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Saveable;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class Project.
 */
class Project
    extends RepositoryAccessible
    implements
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
     * @param RepositoryCollection $repositoryCollection Repository collection
     * @param string               $masterLanguage       Master language
     * @param string[]             $availableLanguages   Available languages
     * @param string[]             $paths                List of paths
     */
    private function __construct(
        RepositoryCollection $repositoryCollection,
        $masterLanguage,
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
     * @return string MasterLanguage
     */
    public function getMasterLanguage()
    {
        return $this->masterLanguage;
    }

    /**
     * Get AvailableLanguages.
     *
     * @return string[] AvailableLanguages
     */
    public function getAvailableLanguages()
    {
        return $this->availableLanguages;
    }

    /**
     * Get Paths.
     *
     * @return string[] Paths
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Get Repositories.
     *
     * @param array $domains   Domains
     * @param array $languages Languages
     *
     * @return Repository[] Repositories
     */
    public function getRepositories(
        array $domains = [],
        array $languages = []
    ) {
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
     * @param array    $domains   Domains
     * @param array    $languages Languages
     * @param callable $filter    Filter function
     *
     * @return Translation[] Translations
     */
    public function getTranslations(
        array $domains = [],
        array $languages = [],
        callable $filter = null
    ) {
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
     * @param string $language Language
     * @param string $key      Key
     *
     * @return Translation Translation
     */
    public function getTranslation(
        $language,
        $key
    ) {
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
     * Get random Translation.
     *
     * @param array $domains   Domains
     * @param array $languages Languages
     *
     * @return Translation Random translation
     *
     * @throws Exception You cannot search missing translations by master language
     */
    public function getRandomMissingTranslation(
        array $domains = [],
        array $languages = []
    ) {
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
            return false;
        }

        $candidateKey = array_rand($candidates);
        $candidate = $candidates[$candidateKey];
        $masterCandidate = $this->getTranslation(
            $this->masterLanguage,
            $candidate['key']
        );

        $candidateTranslation = Translation::create(
            $candidate['key'],
            null,
            $candidate['language']
        );

        $candidateTranslation
            ->setStructure($masterCandidate->getStructure())
            ->setMasterTranslation($masterCandidate);

        return $candidateTranslation;
    }

    /**
     * Add translation.
     *
     * @param Translation $translation Translation
     *
     * @return $this Self object
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

        return $this;
    }

    /**
     * Save structure.
     *
     * @return $this Self object
     */
    public function sort()
    {
        $this
            ->repositoryCollection
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
        $this
            ->repositoryCollection
            ->save();

        return $this;
    }

    /**
     * Create a project from a list of paths.
     *
     * @param string   $masterLanguage     Master language
     * @param string[] $availableLanguages Available languages
     * @param string[] $paths              List of paths
     *
     * @return self new Project instance
     */
    public static function create(
        $masterLanguage,
        array $availableLanguages,
        array $paths
    ) {
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
