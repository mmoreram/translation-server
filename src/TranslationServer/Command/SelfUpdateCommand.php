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

namespace Mmoreram\TranslationServer\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelfUpdateCommand
 */
class SelfUpdateCommand extends Command
{
    const MANIFEST_FILE = 'http://mmoreram.github.io/translation-server/manifest.json';

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates translation-server.phar to the latest version')
            ->setAliases(['selfupdate']);
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input  Input
     * @param OutputInterface $output Output
     *
     * @return int|null|void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('phar://' !== substr(__DIR__, 0, 7)) {
            $output->writeln('<error>Self-update is available only for PHAR version.</error>');

            return 1;
        }

        $manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
        $manager->update($this->getApplication()->getVersion(), true);
    }
}
