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

namespace Mmoreram\TranslationServer\Console;

use Symfony\Component\Console\Application as BaseApplication;

use Mmoreram\TranslationServer\Command;

/**
 * Class Application
 */
class Application extends BaseApplication
{
    /**
     * Construct method
     */
    public function __construct()
    {
        if (function_exists('ini_set') && extension_loaded('xdebug')) {
            ini_set('xdebug.show_exception_trace', false);
            ini_set('xdebug.scream', false);
        }

        if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
            date_default_timezone_set(@date_default_timezone_get());
        }

        parent::__construct('TranslationServer', '@package_version@');
    }

    /**
     * Initializes all the composer commands
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new Command\MetricsCommand();
        $commands[] = new Command\AddCommand();
        $commands[] = new Command\SortCommand();
        $commands[] = new Command\GuessCommand();

        if ('phar://' === substr(__DIR__, 0, 7)) {
            $commands[] = new Command\SelfUpdateCommand();
        }

        return $commands;
    }
}
