<?php

namespace App\Commands\AOC2015;

use LaravelZero\Framework\Commands\Command;

class AOC2015 extends Command
{
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
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->call(AOC2015One::class);
                break;
        }
        $this->handle();
    }
}
