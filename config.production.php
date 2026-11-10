<?php

/*
 * Production. Identical to the base config by design -- the base already holds
 * the production defaults -- but stated explicitly so `jigsaw build production`
 * is self-documenting rather than relying on inheritance.
 *
 * Do NOT add a `collections` key here: the environment merge is shallow, so it
 * would replace the whole collections config rather than extending it.
 */

return [
    'baseUrl' => 'https://aoc.hcaz.uk',
    'production' => true,
    'show_puzzle_text' => false,
];
