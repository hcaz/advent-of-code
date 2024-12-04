<?php

namespace App\Commands\AOC2023;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2023One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2023/01';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2023';

    private Collection $calibrationLines;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2023.01');

        $option = $this->menu($this->challenge['info']['title'], [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        $bench = new Ubench;
        $bench->start();

        $this->title($this->challenge['info']['title']);
        switch ($option) {
            case 0:
                $this->info($this->challenge['info']['link']);
                $this->alert('Step one:');
                $this->info($this->challenge['step_one']);
                $this->alert('Step two:');
                $this->info($this->challenge['step_two']);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $sumOfCalibrationValues = $this->calibrationLines->map(function ($line) {
                    $cleanedLine = preg_replace('/[^0-9]/', '', $line);
                    $firstDigit = substr($cleanedLine, 0, 1);
                    $lastDigit = substr($cleanedLine, -1);

                    return intval("$firstDigit$lastDigit");
                })->sum();

                $this->alert("The sum of all of the calibration values is $sumOfCalibrationValues");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $sumOfCalibrationValues = $this->calibrationLines->map(function ($line) {
                    $firstDigit = null;
                    $lastDigit = null;
                    foreach (str_split($line) as $char) {
                        if (is_numeric($char)) {
                            if (is_null($firstDigit)) {
                                $firstDigit = $char;
                            }
                            $lastDigit = $char;
                        }
                    }

                    return intval("$firstDigit$lastDigit");
                })->sum();

                $this->alert("The sum of all of the calibration values is $sumOfCalibrationValues");
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData()
    {
        $this->info('Loading data...');
        $data = $this->challenge['input'];

        $this->calibrationLines = Collect(explode("\n", $data));

        $this->info("There are {$this->calibrationLines->count()} calibration lines to process");
    }
}
