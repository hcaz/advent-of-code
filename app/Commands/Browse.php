<?php

namespace App\Commands;

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
    public function handle(): void
    {
        $challenges = config('challenges');

        $options = [];
        foreach ($challenges as $year => $days) {
            $complete = array_filter($days, function ($day) {
                return ! empty($day['info']['step_one_answer']) && ! empty($day['info']['step_two_answer']);
            });
            $options[] = "$year - ".count($complete).'/'.count($days).' complete';
        }

        $chooseYear = $this->menu('Advent of Code', $options)->open();

        if (is_null($chooseYear)) {
            $this->info('You have chosen to exit');

            return;
        }
        $year = ($chooseYear + 2015);

        $options = [];
        foreach ($challenges[$year] as $day => $challenge) {
            $options[] = $challenge['info']['title'];
        }

        $chooseDay = $this->menu("Advent of Code - $year", $options)->open();

        if (is_null($chooseDay)) {
            $this->info('You have chosen to exit');

            return;
        }

        $day = str_pad(($chooseDay + 1), 2, '0', STR_PAD_LEFT);

        $this->call("challenge/$year/$day");

        $this->handle();
    }
}
