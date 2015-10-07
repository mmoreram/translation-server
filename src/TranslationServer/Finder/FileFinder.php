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

namespace Mmoreram\TranslationServer\Finder;

use Symfony\Component\Finder\Finder;

/**
 * Class FileFinder
 */
class FileFinder
{
    /**
     * Find all php files by path
     *
     * @param string $path Path
     *
     * @return Finder Finder iterable object with all PHP found files in path
     */
    public function findTranslationsFilesByPaths($path)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name('*.php');

        return $finder;
    }
}
