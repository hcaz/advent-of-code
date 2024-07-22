<?php

namespace App\Commands\AOC2022;

use Carbon\Carbon;
use LaravelZero\Framework\Commands\Command;

class AOC2022 extends Command
{
    public float $complete = 6.5;

    public function available(): int
    {
        $datetime1 = new Carbon('2022-12-01');
        $datetime2 = new Carbon();
        $difference = $datetime1->diff($datetime2);

        return $difference->days > 25 ? 25 : $difference->days;
    }

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022';

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
        $option = $this->menu('2022', [
            'Day 1: Calorie Counting ★★',
            'Day 2: Rock Paper Scissors ★★',
            'Day 3: Rucksack Reorganization ★★',
            'Day 4: Camp Cleanup ★★',
            'Day 5: Supply Stacks ★★',
            'Day 6: Tuning Trouble ★★',
            'Day 7: No Space Left On Device ★',
            'Day 8: Treetop Tree House',
            'Day 9: Rope Bridge',
            'Day 10: Cathode-Ray Tube',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        switch ($option) {
            case 0:
                $this->call(AOC2022One::class);
                break;
            case 1:
                $this->call(AOC2022Two::class);
                break;
            case 2:
                $this->call(AOC2022Three::class);
                break;
            case 3:
                $this->call(AOC2022Four::class);
                break;
            case 4:
                $this->call(AOC2022Five::class);
                break;
            case 5:
                $this->call(AOC2022Six::class);
                break;
            case 6:
                $this->call(AOC2022Seven::class);
                break;
            case 7:
                $this->call(AOC2022Eight::class);
                break;
        }
        $this->handle();
    }
}
