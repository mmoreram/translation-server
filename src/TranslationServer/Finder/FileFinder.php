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

namespace Mmoreram\TranslationServer\Finder;

use Symfony\Component\Finder\Finder;

/**
 * Class FileFinder.
 */
class FileFinder
{
    /**
     * Find all php files by path.
     *
     * @param string $path
     *
     * @return Finder
     */
    public function findTranslationsFilesByPaths(string $path) : Finder
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($path)
            ->name('*.php');

        return $finder;
    }
}
