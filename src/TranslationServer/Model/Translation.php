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

/**
 * Class Translation.
 */
class Translation
{
    /**
     * @var string
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
     * @param string $key      Key
     * @param string $value    Value
     * @param string $language Language
     */
    private function __construct(
        $key,
        $value,
        $language
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->language = $language;
    }

    /**
     * Sets Structure.
     *
     * @param array $structure Structure
     *
     * @return $this Self object
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get Structure.
     *
     * @return array Structure
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * Get Key.
     *
     * @return string Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get Value.
     *
     * @return string Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value.
     *
     * @param string $value Value
     *
     * @return $this Self object
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get MasterTranslation.
     *
     * @return Translation MasterTranslation
     */
    public function getMasterTranslation()
    {
        return $this->masterTranslation;
    }

    /**
     * Sets MasterTranslation.
     *
     * @param Translation $masterTranslation MasterTranslation
     *
     * @return $this Self object
     */
    public function setMasterTranslation($masterTranslation)
    {
        $this->masterTranslation = $masterTranslation;

        return $this;
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
     * Get Repository.
     *
     * @return Repository Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Sets Repository.
     *
     * @param Repository $repository Repository
     *
     * @return $this Self object
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * Create new translation.
     *
     * @param string $key      Key
     * @param string $value    Value
     * @param string $language Language
     *
     * @return self New Translation instance
     */
    public static function create(
        $key,
        $value,
        $language
    ) {
        return new self(
            $key,
            $value,
            $language
        );
    }
}
