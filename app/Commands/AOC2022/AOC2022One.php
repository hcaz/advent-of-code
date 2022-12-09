<?php

namespace App\Commands\AOC2022;

use LaravelZero\Framework\Commands\Command;

class AOC2022One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse 2022 one';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2022';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("2022 - #1");
    }
}
