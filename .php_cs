<?php

return Symfony\CS\Config\Config::create()
    // use SYMFONY_LEVEL:
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    // and extra fixers:
    ->fixers(array(
        'concat_with_spaces',
        'multiline_spaces_before_semicolon',
        'short_array_syntax',
        '-remove_lines_between_uses',
        '-empty_return',
        '-phpdoc_var_without_name',
        '-phpdoc_to_comment'
    ))
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in('src/')
            ->in('tests/')
    )
;
