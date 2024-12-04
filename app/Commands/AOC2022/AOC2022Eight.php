<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Eight extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/08';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 8 :: 2022';

    private Collection $trees;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.08');

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

                $hiddenTrees = 0;
                for ($i = 1; $i < ($this->trees->count() - 1); $i++) {
                    $row = $this->trees[$i];
                    for ($j = 1; $j < ($row->count() - 1); $j++) {
                        $currentTree = $this->trees[$i][$j];
                        $before = $this->trees[$i]->slice(0, $j);
                        $after = $this->trees[$i]->slice($j);
                        dump("{$i},{$j} H {$before->implode('')} {$currentTree} {$after->implode('')}");
                        if ($currentTree >= $before->max() || $currentTree >= $after->max()) {
                            continue;
                        }

                        $tmpColumn = Collect([]);
                        for ($x = 0; $x < $this->trees->count(); $x++) {
                            $tmpColumn->push($this->trees[$x][$j]);
                        }
                        $above = $tmpColumn->slice(0, $i);
                        $below = $tmpColumn->slice($i);
                        dump("{$i},{$j} V {$above->implode('')} {$currentTree} {$below->implode('')}");
                        if ($currentTree >= $above->max() || $currentTree >= $below->max()) {
                            continue;
                        }

                        $hiddenTrees++;
                        exit;
                    }
                }

                $this->alert("There are $hiddenTrees hidden trees");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();
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

        $trees = 0;
        $this->trees = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $tmpTrees = Collect(explode(',', chunk_split($line, 1, ',')));
            $this->trees->push($tmpTrees);

            $trees += $tmpTrees->count();
        }

        $this->info("There are $trees trees in a grid of {$this->trees->count()} rows and {$this->trees->first()->count()} columns");
    }
}
