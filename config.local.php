<?php

/*
 * Local development. The ONLY environment that may render puzzle text.
 *
 * Safe here because the output goes to build_local/, which is gitignored and
 * served only at aoc.test -- it is never committed and never deployed.
 */

return [
    'baseUrl' => 'https://aoc.test',
    'production' => false,
    'show_puzzle_text' => true,
];
