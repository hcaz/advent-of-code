<?php

namespace App\Commands;

use App\Commands\AOC2015\AOC2015;
use App\Commands\AOC2022\AOC2022;
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
        $AOC2015 = new AOC2015();
        $AOC2022 = new AOC2022();
        $totalComplete = $AOC2015->complete + $AOC2022->complete;

        $option = $this->menu("Advent of Code - $totalComplete / 200 complete", [
            "2015 - $AOC2015->complete / 25 complete",
            '2016 - 0 / 25 complete',
            '2017 - 0 / 25 complete',
            '2018 - 0 / 25 complete',
            '2019 - 0 / 25 complete',
            '2020 - 0 / 25 complete',
            '2021 - 0 / 25 complete',
            "2022 - $AOC2022->complete / 25 complete",
        ])->open();

        if (is_null($option)) {
            $this->info('You have chosen to exit');

            return;
        }

        switch($option) {
            case 0:
                $this->call($AOC2015::class);
                break;
            case 7:
                $this->call($AOC2022::class);
                break;
        }
        $this->handle();
    }
}
