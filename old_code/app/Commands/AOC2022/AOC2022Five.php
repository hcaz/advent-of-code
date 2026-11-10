<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Five extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/05';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 5 :: 2022';

    private Collection $stacks;

    private Collection $moves;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.05');

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

                $this->info('Starting position:');
                $this->displayStacks();

                foreach ($this->moves as $move) {
                    for ($i = 0; $i < $move['amount']; $i++) {
                        $crate = $this->stacks[$move['from'] - 1]->pop();
                        $this->stacks[$move['to'] - 1]->push($crate);
                    }
                }

                $this->info('Ending position:');
                $this->displayStacks();

                $this->alert('Final crates at top of stacks: '.$this->stacks->map(function ($stack) {
                    return $stack->pop();
                })->implode(''));
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $this->info('Starting position:');
                $this->displayStacks();

                foreach ($this->moves as $move) {
                    $crates = $this->stacks[$move['from'] - 1]->pop($move['amount']);
                    foreach ($crates->reverse() as $crate) {
                        $this->stacks[$move['to'] - 1]->push($crate);
                    }
                }

                $this->info('Ending position:');
                $this->displayStacks();

                $this->alert('Final crates at top of stacks: '.$this->stacks->map(function ($stack) {
                    return $stack->pop();
                })->implode(''));
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

        $this->stacks = Collect([]);
        $this->moves = Collect([]);
        $stacks = true;
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                $stacks = false;

                continue;
            }

            if ($stacks) {
                if (! str_contains($line, '[')) {
                    continue;
                }

                $stackCount = round(strlen($line) / 4);
                for ($i = 0; $i < $stackCount; $i++) {
                    if (! isset($this->stacks[$i])) {
                        $this->stacks[$i] = Collect([]);
                    }
                    $crate = substr($line, ($i * 4) + 1, 1);
                    if (! empty(trim($crate))) {
                        $this->stacks[$i]->prepend($crate);
                    }
                }
            } else {
                $move = explode(' ', $line);
                $this->moves->push([
                    'amount' => $move[1],
                    'from' => $move[3],
                    'to' => $move[5],
                ]);
            }
        }

        $this->info("There are {$this->stacks->count()} stacks");
        $this->info("There are {$this->moves->count()} moves");
    }

    private function displayStacks()
    {
        $this->table(['Stack', 'Crate'], $this->stacks->map(function ($stack, $key) {
            return [
                $key + 1,
                $stack->implode(' '),
            ];
        })->toArray());
    }
}
