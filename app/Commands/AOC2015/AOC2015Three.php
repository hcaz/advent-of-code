<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Three extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2015/three';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 3 :: 2015';

    private Collection $presents;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 3: Perfectly Spherical Houses in a Vacuum', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        $bench = new Ubench;

        $bench->start();
        switch ($option) {
            case 0:
                $this->alert('https://adventofcode.com/2015/day/3');
                $this->info(<<<'EOL'
--- Day 3: Perfectly Spherical Houses in a Vacuum ---
Santa is delivering presents to an infinite two-dimensional grid of houses.

He begins by delivering a present to the house at his starting location, and then an elf at the North Pole calls him via radio and tells him where to move next. Moves are always exactly one house to the north (^), south (v), east (>), or west (<). After each move, he delivers another present to the house at his new location.

However, the elf back at the north pole has had a little too much eggnog, and so his directions are a little off, and Santa ends up visiting some houses more than once. How many houses receive at least one present?

For example:

> delivers presents to 2 houses: one at the starting location, and one to the east.
^>v< delivers presents to 4 houses in a square, including twice to the house at his starting/ending location.
^v^v^v^v^v delivers a bunch of presents to some very lucky children at only 2 houses.
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
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData()
    {
        $this->info('Running solution for problem 3 :: 2015');
        $this->info('Loading in 2015_three_input.txt');
        $data = Storage::get('2015/three_input.txt');

        $this->presents = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $tmpPresents = Collect(explode('x', $line));
            $this->presents->push($tmpPresents);
        }

        $this->info("There are {$this->presents->count()} presents to wrap");
    }
}
