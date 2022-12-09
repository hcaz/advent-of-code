<?php

namespace App\Commands\AOC2022;

use LaravelZero\Framework\Commands\Command;

class AOC2022 extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse 2022';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solutions 2022';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Please select a problem to view', [
            'Day 1: Calorie Counting',
            'Day 2: Rock Paper Scissors',
            'Day 3: Rucksack Reorganization',
            'Day 4: Camp Cleanup',
            'Day 5: Supply Stacks',
        ])->open();

        if(is_null($option)) {
            $this->info('You have chosen to exit');
            return;
        }

        switch($option) {
            case 0:
                $this->call(AOC2022One::class);
                break;
        }
    }
}
