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

use Mmoreram\TranslationServer\Model\Abstracts\RepositoryAccessible;
use Mmoreram\TranslationServer\Model\Interfaces\Saveable;
use Mmoreram\TranslationServer\Model\Interfaces\Sortable;

/**
 * Class RepositoryCollection.
 */
class RepositoryCollection
    extends RepositoryAccessible
    implements
        Sortable,
        Saveable
{
    /**
     * @var Repository[]
     *
     * Repository
     */
    private $repositories;

    /**
     * Constructor.
     *
     * @param Repository[] $repositories Repositories
     */
    private function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Add repository.
     *
     * @param Repository $repository Repository
     *
     * @return Repository Repository
     */
    public function addRepository(Repository $repository)
    {
        $this->repositories[] = $repository;
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
        $repositories = array_filter(
            $this->repositories,
            function (Repository $repository) use ($domains) {
                return
                    empty($domains) ||
                    in_array($repository->getDomain(), $domains);
            }
        );

        return array_filter(
            $repositories,
            function (Repository $repository) use ($languages) {
                return
                    empty($languages) ||
                    in_array($repository->getLanguage(), $languages);
            }
        );
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
        return array_reduce(
            $this->getRepositories(
                $domains,
                $languages
            ),
            function (array $translations, Repository $repository) use ($domains, $languages, $filter) {
                return array_merge(
                    $translations,
                    $repository->getTranslations(
                        $domains,
                        $languages,
                        $filter
                    )
                );
            },
            []
        );
    }

    /**
     * Save structure.
     *
     * @return $this Self object
     */
    public function sort()
    {
        foreach ($this->getRepositories() as $repository) {
            $repository->sort();
        }
    }

    /**
     * Save structure.
     *
     * @return $this Self object
     */
    public function save()
    {
        foreach ($this->getRepositories() as $repository) {
            $repository->save();
        }
    }

    /**
     * Create new instance of Repository collection.
     *
     * @param Repository[] $repositories Repositories
     *
     * @return self New instance
     */
    public static function create(array $repositories)
    {
        return new self($repositories);
    }
}
