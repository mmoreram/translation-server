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

namespace Mmoreram\TranslationServer\Tests\Model\Abstracts;

use Mmoreram\TranslationServer\Model\Project;
use Mmoreram\TranslationServer\Model\Repository;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractModelTest
 */
abstract class AbstractModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get project
     */
    protected function getProject()
    {
        $paths = [dirname(__FILE__).'/../../../Fixtures'];

        return Project::create(
            'en',
            ['en', 'ca'],
            $paths
        );
    }

    /**
     * Get project
     */
    protected function getRepository()
    {
        $path = dirname(__FILE__).'/../../../Fixtures/domain.ca.yml';

        return Repository::createByFilePath($path);
    }
}
