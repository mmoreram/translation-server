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

use Exception;
use Mmoreram\TranslationServer\TranslationServer;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Class ConfigFinder
 */
class ConfigFinder
{
    /**
     * Load, if exists, specific project configuration
     *
     * @param string $path Path
     *
     * @return array loaded config
     *
     * @throws Exception Config file not found
     */
    public function findConfigFile($path)
    {
        $configFilePath = rtrim($path, '/').'/'.TranslationServer::CONFIG_FILE_NAME;
        if (!is_file($configFilePath)) {
            throw new Exception('File ".translation.yml" not found');
        }

        $yamlParser = new YamlParser();
        $config = $yamlParser->parse(file_get_contents($configFilePath));

        return $config;
    }
}
