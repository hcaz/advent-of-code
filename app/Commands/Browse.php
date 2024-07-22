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
        $year = 2015;
        foreach ($challenges as $days) {
            $complete = array_filter($days, function ($day) {
                return ! empty($day['info']['step_one_answer']) && ! empty($day['info']['step_two_answer']);
            });
            $title = "AOC ".$year++;

            if(count($days) == 0) {
                $title .= ' [Event not started]';
            } elseif(count($complete) == count($days)) {
                $title .= ' [Fully Complete]';
            } elseif(count($complete) > 0) {
                $title .= ' [Partially Complete] ' . count($complete).' complete out of '.count($days);
            }

            $options[] = $title;
        }

        $chooseYear = $this->menu('Advent of Code', $options)->open();

        if (is_null($chooseYear)) {
            $this->info('You have chosen to exit');

            return;
        }
        $year = ($chooseYear + 2015);

        $options = [];
        foreach ($challenges[$year] as $day => $challenge) {
            $title = $challenge['info']['title'];
            if (!empty($challenge['info']['step_one_answer']) && !empty($challenge['info']['step_two_answer'])) {
                $title .= " [Fully Complete] ";
            }elseif(!empty($challenge['info']['step_one_answer']) || !empty($challenge['info']['step_two_answer'])) {
                $title .= " [Partially Complete] ";
            }
            if(!empty($challenge['info']['step_one_answer'])) {
                $title .= '★';
            }
            if(!empty($challenge['info']['step_two_answer'])) {
                $title .= '★';
            }
            $options[] = $title;
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
