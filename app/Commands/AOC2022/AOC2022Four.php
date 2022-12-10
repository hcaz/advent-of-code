<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class AOC2022Four extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022/four';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 4 :: 2022';

    private Collection $sections;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 4: Camp Cleanup', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText("Back")->open();

        if(is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/4');
                $this->info(<<<EOL
--- Day 4: Camp Cleanup ---
Space needs to be cleared before the last supplies can be unloaded from the ships, and so several Elves have been assigned the job of cleaning up sections of the camp. Every section has a unique ID number, and each Elf is assigned a range of section IDs.

However, as some of the Elves compare their section assignments with each other, they've noticed that many of the assignments overlap. To try to quickly find overlaps and reduce duplicated effort, the Elves pair up and make a big list of the section assignments for each pair (your puzzle input).

For example, consider the following list of section assignment pairs:

----
2-4,6-8
2-3,4-5
5-7,7-9
2-8,3-7
6-6,4-6
2-6,4-8
----

For the first few pairs, this list means:
- Within the first pair of Elves, the first Elf was assigned sections 2-4 (sections 2, 3, and 4), while the second Elf was assigned sections 6-8 (sections 6, 7, 8).
- The Elves in the second pair were each assigned two sections.
- The Elves in the third pair were each assigned three sections: one got sections 5, 6, and 7, while the other also got 7, plus 8 and 9.

This example list uses single-digit section IDs to make it easier to draw; your actual list might contain larger numbers. Visually, these pairs of section assignments look like this:

----
.234.....  2-4
.....678.  6-8

.23......  2-3
...45....  4-5

....567..  5-7
......789  7-9

.2345678.  2-8
..34567..  3-7

.....6...  6-6
...456...  4-6

.23456...  2-6
...45678.  4-8
----

Some of the pairs have noticed that one of their assignments fully contains the other. For example, 2-8 fully contains 3-7, and 6-6 is fully contained by 4-6. In pairs where one assignment fully contains the other, one Elf in the pair would be exclusively cleaning sections their partner will already be cleaning, so these seem like the most in need of reconsideration. In this example, there are 2 such pairs.

In how many assignment pairs does one range fully contain the other?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();
                break;
        }

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData() {
        $this->info("Running solution for problem 4 :: 2022");
        $this->info("Loading in 2022_four_input.txt");
        $data = Storage::get('2022/four_input.txt');

        $this->sections = Collect([]);
        foreach(explode("\n", $data) as $line) {
            if($line == '') continue;

            $section = explode(',', $line);
            if(count($section) == 2) $this->sections->push(['one' => $section[0], 'two' => $section[1]]);
        }

        $this->info("There are {$this->sections->count()} sections");
    }
}
