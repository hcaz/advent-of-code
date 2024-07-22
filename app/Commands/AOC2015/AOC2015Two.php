<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Two extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2015/two';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 2 :: 2015';

    private Collection $presents;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 2: I Was Told There Would Be No Math', [
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
                $this->alert('https://adventofcode.com/2015/day/2');
                $this->info(<<<'EOL'
--- Day 2: I Was Told There Would Be No Math ---
The elves are running low on wrapping paper, and so they need to submit an order for more. They have a list of the dimensions (length l, width w, and height h) of each present, and only want to order exactly as much as they need.

Fortunately, every present is a box (a perfect right rectangular prism), which makes calculating the required wrapping paper for each gift a little easier: find the surface area of the box, which is 2*l*w + 2*w*h + 2*h*l. The elves also need a little extra paper for each present: the area of the smallest side.

For example:

A present with dimensions 2x3x4 requires 2*6 + 2*12 + 2*8 = 52 square feet of wrapping paper plus 6 square feet of slack, for a total of 58 square feet.
A present with dimensions 1x1x10 requires 2*1 + 2*10 + 2*10 = 42 square feet of wrapping paper plus 1 square foot of slack, for a total of 43 square feet.
All numbers in the elves' list are in feet. How many total square feet of wrapping paper should they order?

--- Part Two ---
The elves are also running low on ribbon. Ribbon is all the same width, so they only have to worry about the length they need to order, which they would again like to be exact.

The ribbon required to wrap a present is the shortest distance around its sides, or the smallest perimeter of any one face. Each present also requires a bow made out of ribbon as well; the feet of ribbon required for the perfect bow is equal to the cubic feet of volume of the present. Don't ask how they tie the bow, though; they'll never tell.

For example:

A present with dimensions 2x3x4 requires 2+2+3+3 = 10 feet of ribbon to wrap the present plus 2*3*4 = 24 feet of ribbon for the bow, for a total of 34 feet.
A present with dimensions 1x1x10 requires 1+1+1+1 = 4 feet of ribbon to wrap the present plus 1*1*10 = 10 feet of ribbon for the bow, for a total of 14 feet.
How many total feet of ribbon should they order?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $totalFeetOfWrappingPaper = 0;

                foreach ($this->presents as $present) {
                    $totalFeetOfWrappingPaper += 2 * $present[0] * $present[1];
                    $totalFeetOfWrappingPaper += 2 * $present[1] * $present[2];
                    $totalFeetOfWrappingPaper += 2 * $present[2] * $present[0];

                    $totalFeetOfWrappingPaper += min($present[0] * $present[1], $present[1] * $present[2], $present[2] * $present[0]);
                }

                $this->info("The elves should order $totalFeetOfWrappingPaper square feet of wrapping paper");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $totalFeetOfRibbon = 0;

                foreach ($this->presents as $present) {
                    $totalFeetOfRibbon += 2 * min($present[0] + $present[1], $present[1] + $present[2], $present[2] + $present[0]);
                    $totalFeetOfRibbon += $present[0] * $present[1] * $present[2];
                }

                $this->info("The elves should order $totalFeetOfRibbon feet of ribbon");
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
        $this->info('Running solution for problem 2 :: 2015');
        $this->info('Loading in 2015_two_input.txt');
        $data = Storage::get('2015/two_input.txt');

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
