<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Six extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/06';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 6 :: 2022';

    private string $buffer;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.06');

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

                $indexOfFirstMarker = null;

                $localBuffer = Collect([]);
                for ($i = 0; $i < strlen($this->buffer); $i++) {
                    $char = $this->buffer[$i];
                    if (empty(trim($char))) {
                        continue;
                    }

                    $localBuffer->push($char);
                    if ($localBuffer->count() > 4) {
                        $localBuffer->pull(0);
                    }
                    $localBuffer = $localBuffer->values();

                    if ($localBuffer->count() == 4 && $localBuffer->unique()->count() == 4) {
                        //                        $this->info("Found 4 unique characters in a row {$localBuffer->implode('')} at index " . ($i + 1));

                        if (is_null($indexOfFirstMarker)) {
                            $indexOfFirstMarker = ($i + 1);
                        }
                    }
                }

                if (is_null($indexOfFirstMarker)) {
                    $this->error('No start-of-packet found');
                } else {
                    $this->alert("Found start-of-packet found at index $indexOfFirstMarker");
                }
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $indexOfFirstMarker = null;

                $localBuffer = Collect([]);
                for ($i = 0; $i < strlen($this->buffer); $i++) {
                    $char = $this->buffer[$i];
                    if (empty(trim($char))) {
                        continue;
                    }

                    $localBuffer->push($char);
                    if ($localBuffer->count() > 14) {
                        $localBuffer->pull(0);
                    }
                    $localBuffer = $localBuffer->values();

                    if ($localBuffer->count() == 14 && $localBuffer->unique()->count() == 14) {
                        //                        $this->info("Found 14 unique characters in a row {$localBuffer->implode('')} at index " . ($i + 1));

                        if (is_null($indexOfFirstMarker)) {
                            $indexOfFirstMarker = ($i + 1);
                        }
                    }
                }

                if (is_null($indexOfFirstMarker)) {
                    $this->error('No start-of-packet found');
                } else {
                    $this->alert("Found start-of-packet found at index $indexOfFirstMarker");
                }
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

        $this->buffer = $data;
    }
}
