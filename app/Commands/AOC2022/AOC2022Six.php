<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class AOC2022Six extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022/six';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 6 :: 2022';

    private String $buffer;
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 6: Title', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText("Back")->open();

        if(is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/6');
                $this->info(<<<EOL

EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();
                break;
        }

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData() {
        $this->info("Running solution for problem 6 :: 2022");
        $this->info("Loading in 2022_six_input.txt");
        $data = Storage::get('2022/six_input.txt');

        $this->buffer = $data;
    }
}
