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

--- Part Two ---
It seems like there is still quite a bit of duplicate work planned. Instead, the Elves would like to know the number of pairs that overlap at all.

In the above example, the first two pairs (2-4,6-8 and 2-3,4-5) don't overlap, while the remaining four pairs (5-7,7-9, 2-8,3-7, 6-6,4-6, and 2-6,4-8) do overlap:
- 5-7,7-9 overlaps in a single section, 7.
- 2-8,3-7 overlaps all of the sections 3 through 7.
- 6-6,4-6 overlaps in a single section, 6.
- 2-6,4-8 overlaps in sections 4, 5, and 6.

So, in this example, the number of overlapping assignment pairs is 4.

In how many assignment pairs do the ranges overlap?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $overlaps = 0;
                foreach($this->sections as $section) {
                    $one = explode('-', $section['one']);
                    $two = explode('-', $section['two']);

                    if($one[0] <= $two[0] && $one[1] >= $two[1]) {
                        $overlaps++;
                    } elseif($two[0] <= $one[0] && $two[1] >= $one[1]) {
                        $overlaps++;
                    }
                }

                $this->alert("There are $overlaps pairs with overlapping sections");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $overlaps = 0;
                foreach($this->sections as $section) {
                    $one = explode('-', $section['one']);
                    $two = explode('-', $section['two']);

                    if($one[0] <= $two[0] && $one[1] >= $two[1]) {
                        $overlaps++;
                    } elseif($two[0] <= $one[0] && $two[1] >= $one[1]) {
                        $overlaps++;
                    } elseif($one[0] <= $two[0] && $one[1] >= $two[0]) {
                        $overlaps++;
                    } elseif($two[0] <= $one[0] && $two[1] >= $one[0]) {
                        $overlaps++;
                    }
                }

                $this->alert("There are $overlaps pairs with overlapping sections");
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
