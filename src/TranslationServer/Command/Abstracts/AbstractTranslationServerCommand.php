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

namespace Mmoreram\TranslationServer\Command\Abstracts;

use Elcodi\Component\Core\Command\Abstracts\AbstractElcodiCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Mmoreram\TranslationServer\Finder\ConfigFinder;
use Mmoreram\TranslationServer\Loader\ConfigLoader;
use Mmoreram\TranslationServer\Model\Project;

/**
 * Class AbstractTranslationServerCommand
 */
class AbstractTranslationServerCommand extends AbstractElcodiCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->addOption(
                '--config',
                '-c',
                InputOption::VALUE_OPTIONAL,
                "Config file directory",
                getcwd()
            )
            ->addOption(
                '--domain',
                '-d',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                "Desired domains",
                []
            )
            ->addOption(
                '--language',
                '-l',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                "Desired languages",
                []
            );
    }

    /**
     * Create project given an input instance
     *
     * @param InputInterface $input Input
     *
     * @return Project New project instance
     */
    public function createProject(InputInterface $input)
    {
        $configFinder = new ConfigFinder();
        $configLoader = new ConfigLoader();

        /**
         * This section is just for finding the right values to work with in
         * this execution.
         *
         * $options array will have, after this block, all these values
         */
        $configPath = rtrim($input->getOption('config'), DIRECTORY_SEPARATOR);
        $configValues = $configLoader->loadConfigValues(
            $configFinder->findConfigFile($configPath)
        );

        $masterLanguage = $configValues['master_language'];
        $languages      = $configValues['languages'];
        $filePaths      = $configValues['paths'];
        $exportPath     = $configValues['export_path'];

        return Project::create(
            $masterLanguage,
            $languages,
            $filePaths,
            $exportPath
        );
    }

    /**
     * Get project header
     *
     * @return string Get project header
     */
    protected function getProjectHeader()
    {
        return 'Trans Server';
    }
}
