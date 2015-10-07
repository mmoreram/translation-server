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

use Exception;
use Mmoreram\TranslationServer\Command\Abstracts\AbstractTranslationServerCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SortCommand
 */
class SortCommand extends AbstractTranslationServerCommand
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('translation:server:sort')
            ->setDescription('Sort translations');

        parent::configure();
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
        $this->startCommand($output, false);
        $this
            ->createProject($input)
            ->sort()
            ->save();

        $this
            ->printMessage(
                $output,
                'Trans Server',
                'Your translations have been sorted successfuly'
            );

        $this->finishCommand($output);
    }
}
