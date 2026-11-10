<?php

/*
 * Staging. Inherits the deny-by-default puzzle text gate from config.php,
 * which is the point of that default: this file says nothing about
 * show_puzzle_text, so it cannot accidentally be permissive.
 *
 * baseUrl matches production for now, so links resolve. If a dedicated
 * staging domain is set up, change it here.
 *
 * Do NOT add a `collections` key -- the environment merge is shallow and
 * would replace the whole collections config.
 */

return [
    'baseUrl' => 'https://staging.aoc.hcaz.uk',
    'production' => true,
    'show_puzzle_text' => false,
];
