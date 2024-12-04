<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Four extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/04';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 4 :: 2022';

    private Collection $sections;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.04');

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

                $overlaps = 0;
                foreach ($this->sections as $section) {
                    $one = explode('-', $section['one']);
                    $two = explode('-', $section['two']);

                    if ($one[0] <= $two[0] && $one[1] >= $two[1]) {
                        $overlaps++;
                    } elseif ($two[0] <= $one[0] && $two[1] >= $one[1]) {
                        $overlaps++;
                    }
                }

                $this->alert("There are $overlaps pairs with overlapping sections");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $overlaps = 0;
                foreach ($this->sections as $section) {
                    $one = explode('-', $section['one']);
                    $two = explode('-', $section['two']);

                    if ($one[0] <= $two[0] && $one[1] >= $two[1]) {
                        $overlaps++;
                    } elseif ($two[0] <= $one[0] && $two[1] >= $one[1]) {
                        $overlaps++;
                    } elseif ($one[0] <= $two[0] && $one[1] >= $two[0]) {
                        $overlaps++;
                    } elseif ($two[0] <= $one[0] && $two[1] >= $one[0]) {
                        $overlaps++;
                    }
                }

                $this->alert("There are $overlaps pairs with overlapping sections");
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

        $this->sections = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $section = explode(',', $line);
            if (count($section) == 2) {
                $this->sections->push(['one' => $section[0], 'two' => $section[1]]);
            }
        }

        $this->info("There are {$this->sections->count()} sections");
    }
}
