<?php

namespace App\Commands\AOC2015;

use Carbon\Carbon;
use LaravelZero\Framework\Commands\Command;

class AOC2015 extends Command
{
    public float $complete = 1.5;
    public function available():int {
        $datetime1 = new Carbon('2015-12-01');
        $datetime2 = new Carbon();
        $difference = $datetime1->diff($datetime2);

        return $difference->days > 25 ? 25 : $difference->days;
    }
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2015';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solutions 2015';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('2015', [
            'Day 1: Not Quite Lisp ★★',
            'Day 2: I Was Told There Would Be No Math ★',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->call(AOC2015One::class);
                break;
            case 1:
                $this->call(AOC2015Two::class);
                break;
        }
        $this->handle();
    }
}
