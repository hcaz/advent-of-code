<?php

namespace App\Commands;

use App\Commands\AOC2022\AOC2022;
use App\Commands\AOC2022\AOC2022Two;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Browse extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display main menu navigating Advent of Code solutions';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Advent of Code', [
            '2022',
        ])->open();

        if(is_null($option)) {
            $this->info('You have chosen to exit');
            return;
        }

        switch($option) {
            case 0:
                $this->call(AOC2022::class);
                break;
        }
    }
}
