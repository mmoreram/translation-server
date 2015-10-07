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

namespace Mmoreram\TranslationServer\Tests\Model;

use Mmoreram\TranslationServer\Model\Project;
use Mmoreram\TranslationServer\Tests\Model\Abstracts\AbstractModelTest;

/**
 * Class ProjectTest
 */
class ProjectTest extends AbstractModelTest
{
    /**
     * Test creation by paths
     *
     * @dataProvider dataCreateProjectByPaths
     */
    public function testCreateProjectByPaths(
        array $domains,
        array $languages,
        $value
    ) {
        $project = $this->getProject();
        $this->assertCount(
            $value,
            $project->getRepositories(
                $domains,
                $languages
            )
        );
    }

    /**
     * Data for testCreateProjectByPaths
     */
    public function dataCreateProjectByPaths()
    {
        return [
            [['domain'], ['ca'], 1],
            [['domain'], ['en', 'ca'], 2],
            [['domain'], [], 2],
            [[], [], 2],
            [[], ['fr'], 0],
            [['domain'], ['fr'], 0],
            [['anotherdomain'], ['ca'], 0],
            [['anotherdomain'], [], 0],
        ];
    }

    /**
     * Test get random translation
     */
    public function testGetRandomTranslation()
    {
        $project = $this->getProject();
        $this->assertInstanceOf(
            'Mmoreram\TranslationServer\Model\Translation',
            $project->getRandomMissingTranslation()
        );
    }
}
