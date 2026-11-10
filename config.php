<?php

/*
 * Base config doubles as the PRODUCTION defaults.
 *
 * This matters for security. BuildCommand::includeEnvironmentConfig() merges
 * config.<env>.php over this file by top-level key and drops nulls, so a key
 * MISSING from an environment config silently inherits whatever is here. If the
 * permissive value lived here, a new or mistyped environment would quietly
 * publish puzzle text. So the defaults deny, and only config.local.php opts in.
 */

return [
    'baseUrl' => 'https://aoc.hcaz.uk',
    'production' => true,

    // Gate for puzzle inputs and puzzle prose. See src/Data.php.
    // NEVER default this to true.
    'show_puzzle_text' => false,

    // Named site_name, not title: Jigsaw merges site config into page
    // variables, so a key called `title` would shadow every page's own title
    // and a problem with no title would silently inherit the site name.
    'site_name' => 'Advent of Code',
    'description' => 'My Advent of Code solutions, every year since 2015.',

    'collections' => [
        'problems' => [
            'path' => '{year}/{day}',
            'sort' => ['-year', 'day'],
        ],
        'years' => [
            'path' => '{filename}',
        ],
    ],
];
