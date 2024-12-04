<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Six extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/06';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 6 :: 2015';

    private Collection $instructions;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2015.06');

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

                $lights = [];
                foreach (range(0, 999) as $x) {
                    foreach (range(0, 999) as $y) {
                        $lights[$x][$y] = 0;
                    }
                }

                foreach ($this->instructions as $instruction) {
                    preg_match('/(turn on|turn off|toggle) (\d+),(\d+) through (\d+),(\d+)/', $instruction, $matches);
                    $action = $matches[1];
                    $startX = $matches[2];
                    $startY = $matches[3];
                    $endX = $matches[4];
                    $endY = $matches[5];

                    foreach (range($startX, $endX) as $x) {
                        foreach (range($startY, $endY) as $y) {
                            switch ($action) {
                                case 'turn on':
                                    $lights[$x][$y] = 1;
                                    break;
                                case 'turn off':
                                    $lights[$x][$y] = 0;
                                    break;
                                case 'toggle':
                                    $lights[$x][$y] = $lights[$x][$y] === 1 ? 0 : 1;
                                    break;
                            }
                        }
                    }
                }

                $this->alert('Total lights on: '.collect($lights)->flatten()->sum());

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $lights = [];
                foreach (range(0, 999) as $x) {
                    foreach (range(0, 999) as $y) {
                        $lights[$x][$y] = 0;
                    }
                }

                foreach ($this->instructions as $instruction) {
                    preg_match('/(turn on|turn off|toggle) (\d+),(\d+) through (\d+),(\d+)/', $instruction, $matches);
                    $action = $matches[1];
                    $startX = $matches[2];
                    $startY = $matches[3];
                    $endX = $matches[4];
                    $endY = $matches[5];

                    foreach (range($startX, $endX) as $x) {
                        foreach (range($startY, $endY) as $y) {
                            switch ($action) {
                                case 'turn on':
                                    $lights[$x][$y]++;
                                    break;
                                case 'turn off':
                                    if ($lights[$x][$y] > 0) {
                                        $lights[$x][$y]--;
                                    }
                                    break;
                                case 'toggle':
                                    $lights[$x][$y] += 2;
                                    break;
                            }
                        }
                    }
                }

                $this->alert('Total light brightness: '.collect($lights)->flatten()->sum());

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
        $data = trim($this->challenge['input']);
        $this->instructions = collect(explode("\n", $data));

        $this->info('Loaded '.$this->instructions->count().' instructions to process');
    }
}
