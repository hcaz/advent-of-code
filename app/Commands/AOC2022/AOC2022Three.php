<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class AOC2022Three extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse 2022 three';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 3 :: 2022';

    private Collection $rucksacks;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 3: Rucksack Reorganization', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->open();

        if(is_null($option)) {
            $this->info('You have chosen to exit');
            return;
        }

        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/3');
                $this->info(<<<EOL
--- Day 3: Rucksack Reorganization ---
One Elf has the important job of loading all of the rucksacks with supplies for the jungle journey. Unfortunately, that Elf didn't quite follow the packing instructions, and so a few items now need to be rearranged.

Each rucksack has two large compartments. All items of a given type are meant to go into exactly one of the two compartments. The Elf that did the packing failed to follow this rule for exactly one item type per rucksack.

The Elves have made a list of all of the items currently in each rucksack (your puzzle input), but they need your help finding the errors. Every item type is identified by a single lowercase or uppercase letter (that is, a and A refer to different types of items).

The list of items for each rucksack is given as characters all on a single line. A given rucksack always has the same number of items in each of its two compartments, so the first half of the characters represent items in the first compartment, while the second half of the characters represent items in the second compartment.

For example, suppose you have the following list of contents from six rucksacks:

----
vJrwpWtwJgWrhcsFMMfFFhFp
jqHRNqRjqzjGDLGLrsFMfFZSrLrFZsSL
PmmdzqPrVvPwwTWBwg
wMqvLMZHhHMvwLHjbvcjnnSBnvTQFn
ttgJtRGJQctTZtZT
CrZsJsPPZsGzwwsLwLmpwMDw
----

- The first rucksack contains the items vJrwpWtwJgWrhcsFMMfFFhFp, which means its first compartment contains the items vJrwpWtwJgWr, while the second compartment contains the items hcsFMMfFFhFp. The only item type that appears in both compartments is lowercase p.
- The second rucksack's compartments contain jqHRNqRjqzjGDLGL and rsFMfFZSrLrFZsSL. The only item type that appears in both compartments is uppercase L.
- The third rucksack's compartments contain PmmdzqPrV and vPwwTWBwg; the only common item type is uppercase P.
- The fourth rucksack's compartments only share item type v.
- The fifth rucksack's compartments only share item type t.
- The sixth rucksack's compartments only share item type s.

To help prioritize item rearrangement, every item type can be converted to a priority:

- Lowercase item types a through z have priorities 1 through 26.
- Uppercase item types A through Z have priorities 27 through 52.

In the above example, the priority of the item type that appears in both compartments of each rucksack is 16 (p), 38 (L), 42 (P), 22 (v), 20 (t), and 19 (s); the sum of these is 157.

Find the item type that appears in both compartments of each rucksack. What is the sum of the priorities of those item types?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $priorityTotal = 0;
                foreach($this->rucksacks as $rucksack) {
                    $itemsInBothCompartments = implode(array_unique(array_intersect(str_split($rucksack['compartment_1']), str_split($rucksack['compartment_2']))));

                    // I had hoped I could change 96 to 64 depending on capital or lowercase, but that didn't work
                    $number = (ord(strtolower($itemsInBothCompartments)) - 96) + ($itemsInBothCompartments == strtoupper($itemsInBothCompartments) ? 26 : 0);
                    if($number > 0) $priorityTotal = $priorityTotal + $number;
                }

                $priorityTotal = number_format($priorityTotal);

                $this->alert("The total priority is {$priorityTotal}");
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
        $this->info("Running solution for problem 3 :: 2022");
        $this->info("Loading in 2022_three_input.txt");
        $data = Storage::get('2022/three_input.txt');

        $this->rucksacks = Collect([]);
        foreach(explode("\n", $data) as $line) {
            $first_half = substr($line,0, strlen($line)/2);
            $second_half = substr($line, strlen($line)/2);

            $this->rucksacks->push([
                'compartment_1' => $first_half,
                'compartment_2' => $second_half,
            ]);
        }

        $this->info("There are {$this->rucksacks->count()} rucksacks");
    }
}
