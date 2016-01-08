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

namespace Mmoreram\TranslationServer\Loader;

use Exception;

/**
 * Class ConfigLoader
 */
class ConfigLoader
{
    /**
     * This method parses the config file, if exists, and determines the real
     * options values.
     *
     * @param array $configValues Config values
     *
     * @return array Usable values
     *
     * @throws Exception Default locale not found
     * @throws Exception Paths not found
     */
    public function loadConfigValues(array $configValues)
    {
        if (!isset($configValues['master_language'])) {
            throw new Exception('Your configuration file must define a master_language');
        }

        if (
            !isset($configValues['languages']) ||
            !is_array($configValues['languages'])
        ) {
            throw new Exception('Your configuration file must define a set languages for translations');
        }

        if (
            !isset($configValues['paths']) ||
            !is_array($configValues['paths'])
        ) {
            throw new Exception('Your configuration file must define a set of paths where to find translations');
        }

        if (!isset($configValues['export_path'])) {
            $configValues['export_path'] = null;
        }

        return $configValues;
    }
}
