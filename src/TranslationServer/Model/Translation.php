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

/**
 * Class Translation.
 */
class Translation
{
    /**
     * @var string|int
     *
     * Key
     */
    private $key;

    /**
     * @var array
     *
     * Structure
     */
    private $structure;

    /**
     * @var string
     *
     * Value
     */
    private $value;

    /**
     * @var string
     *
     * Language
     */
    private $language;

    /**
     * @var Repository
     *
     * Repository
     */
    private $repository;

    /**
     * @var Translation
     *
     * Master translation
     */
    private $masterTranslation;

    /**
     * Constructor.
     *
     * @param string|int $key
     * @param mixed      $value
     * @param string     $language
     */
    private function __construct(
        $key,
        $value,
        string $language
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->language = $language;
    }

    /**
     * Sets Structure.
     *
     * @param array $structure
     */
    public function setStructure(array $structure)
    {
        $this->structure = $structure;
    }

    /**
     * Get Structure.
     *
     * @return array
     */
    public function getStructure() : array
    {
        return $this->structure;
    }

    /**
     * Get Key.
     *
     * @return string|int
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get Value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get MasterTranslation.
     *
     * @return Translation
     */
    public function getMasterTranslation() : Translation
    {
        return $this->masterTranslation;
    }

    /**
     * Sets MasterTranslation.
     *
     * @param Translation $masterTranslation
     */
    public function setMasterTranslation(Translation $masterTranslation)
    {
        $this->masterTranslation = $masterTranslation;
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
     * Get Repository.
     *
     * @return Repository
     */
    public function getRepository() : Repository
    {
        return $this->repository;
    }

    /**
     * Sets Repository.
     *
     * @param Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create new translation.
     *
     * @param string|int $key
     * @param mixed      $value
     * @param string     $language
     *
     * @return Translation
     */
    public static function create(
        $key,
        $value,
        string $language
    ) {
        return new self(
            $key,
            $value,
            $language
        );
    }
}
