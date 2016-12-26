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

namespace Mmoreram\TranslationServer\Tests\Model;

use Mmoreram\TranslationServer\Model\Translation;
use Mmoreram\TranslationServer\Tests\Model\Abstracts\AbstractModelTest;

/**
 * Class RepositoryTest.
 */
class RepositoryTest extends AbstractModelTest
{
    /**
     * Test creation by file path.
     */
    public function testCreateByFilePath()
    {
        $repository = $this->getRepository();
        $translations = $repository->getTranslations();

        $this->assertCount(4, $translations);
        $this->assertEquals('domain', $repository->getDomain());
        $this->assertEquals('ca', $repository->getLanguage());

        /**
         * @var Translation
         */
        $firstTranslation = reset($translations);

        $this->assertEquals('example2.even.another.subdomain.key4', $firstTranslation->getKey());
        $this->assertEquals('valor4', $firstTranslation->getValue());
        $this->assertEquals([
            'example2' => [
                'even' => [
                    'another' => [
                        'subdomain' => [
                            'key4' => 'valor4',
                        ],
                    ],
                ],
            ],
        ], $firstTranslation->getStructure());
    }

    /**
     * Test sorting Repository.
     */
    public function testSort()
    {
        $repository = $this->getRepository();
        $repository->sort();
        $translations = $repository->getTranslations();

        $this->assertCount(4, $translations);

        /**
         * @var Translation
         */
        $firstTranslation = reset($translations);
        $this->assertEquals('example.another.subdomain.key3', $firstTranslation->getKey());
    }

    /**
     * Test add translation.
     */
    public function testAddTranslation()
    {
        $repository = $this->getRepository();
        $translation = $this->createMock('Mmoreram\TranslationServer\Model\Translation');
        $repository->addTranslation($translation);

        $translation
            ->expects($this->any())
            ->method('getValue')
            ->willReturn('value');
        $this->assertCount(5, $repository->getTranslations());
    }
}
