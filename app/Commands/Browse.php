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
        $AOC2016 = new AOC2015();
        $AOC2017 = new AOC2015();
        $AOC2018 = new AOC2015();
        $AOC2019 = new AOC2015();
        $AOC2020 = new AOC2015();
        $AOC2021 = new AOC2015();
        $AOC2022 = new AOC2022();
        $totalComplete = $AOC2015->complete + $AOC2016->complete + $AOC2017->complete + $AOC2018->complete + $AOC2019->complete + $AOC2020->complete + $AOC2021->complete + $AOC2022->complete;
        $totalAvailable = $AOC2015->available() + $AOC2016->available() + $AOC2017->available() + $AOC2018->available() + $AOC2019->available() + $AOC2020->available() + $AOC2021->available() + $AOC2022->available();

        $option = $this->menu("Advent of Code - $totalComplete / $totalAvailable complete", [
            "2015 - $AOC2015->complete / {$AOC2015->available()} complete",
            "2016 - 0 / {$AOC2016->available()} complete",
            "2017 - 0 / {$AOC2017->available()} complete",
            "2018 - 0 / {$AOC2018->available()} complete",
            "2019 - 0 / {$AOC2019->available()} complete",
            "2020 - 0 / {$AOC2020->available()} complete",
            "2021 - 0 / {$AOC2021->available()} complete",
            "2022 - $AOC2022->complete / {$AOC2022->available()} complete",
        ])->open();

        if (is_null($option)) {
            $this->info('You have chosen to exit');

            return;
        }

        switch($option) {
            case 0:
                $this->call($AOC2015::class);
                break;
            case 1:
                $this->call($AOC2016::class);
                break;
            case 2:
                $this->call($AOC2017::class);
                break;
            case 3:
                $this->call($AOC2018::class);
                break;
            case 4:
                $this->call($AOC2019::class);
                break;
            case 5:
                $this->call($AOC2020::class);
                break;
            case 6:
                $this->call($AOC2021::class);
                break;
            case 7:
                $this->call($AOC2022::class);
                break;
        }
        $this->handle();
    }
}
