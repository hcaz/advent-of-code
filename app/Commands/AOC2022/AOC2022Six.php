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

                $indexOfFirstMarker = null;

                $localBuffer = Collect([]);
                for($i = 0; $i < strlen($this->buffer); $i++) {
                    $char = $this->buffer[$i];
                    if(empty(trim($char))) continue;

                    $localBuffer->push($char);
                    if($localBuffer->count() > 4) $localBuffer->pull(0);
                    $localBuffer = $localBuffer->values();

                    if($localBuffer->count() == 4 && $localBuffer->unique()->count() == 4) {
//                        $this->info("Found 4 unique characters in a row {$localBuffer->implode('')} at index " . ($i + 1));

                        if(is_null($indexOfFirstMarker)) $indexOfFirstMarker = ($i + 1);
                    }
                }

                if(is_null($indexOfFirstMarker)) {
                    $this->error("No marker found");
                } else {
                    $this->info("Marker found at index $indexOfFirstMarker");
                }
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
