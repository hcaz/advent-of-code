<?php

namespace App\Commands\AOC2023;

use Carbon\Carbon;
use LaravelZero\Framework\Commands\Command;

class AOC2023 extends Command
{
    public float $complete = 0;

    public function available(): int
    {
        $datetime1 = new Carbon('2023-12-01');
        $datetime2 = new Carbon();
        $difference = $datetime1->diff($datetime2);

        return $difference->days > 25 ? 25 : $difference->days;
    }

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2023';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solutions 2023';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('2023', [
            'Day 1: Trebuchet?! ★',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        switch ($option) {
            case 0:
                $this->call(AOC2023One::class);
                break;
        }
        $this->handle();
    }
}
