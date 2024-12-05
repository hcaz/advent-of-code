<?php

namespace App\Commands\AOC2024;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Five extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/05';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 5 :: 2024';

    private Collection $rules;

    private Collection $updates;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.05');

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

                $subOfMiddleNumbersInOrder = 0;

                foreach ($this->updates as $update) {
                    if (is_null($this->checkRules($update))) {
                        $indexOfMiddle = $update->get($update->count() / 2);
                        $subOfMiddleNumbersInOrder += $indexOfMiddle;
                    }
                }

                $this->alert('Sum of correctly ordered updates: '.$subOfMiddleNumbersInOrder);
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $subOfMiddleNumbersInOrder = 0;

                foreach ($this->updates as $update) {
                    $brokenRules = $this->checkRules($update);
                    if (! is_null($brokenRules)) {
                        while (! is_null($brokenRules)) {
                            $firstNumber = $update->get($brokenRules[0]);
                            $secondNumber = $update->get($brokenRules[1]);
                            $update->forget($brokenRules[0]);
                            $update->forget($brokenRules[1]);
                            $update->put($brokenRules[0], $secondNumber);
                            $update->put($brokenRules[1], $firstNumber);

                            $brokenRules = $this->checkRules($update);
                        }

                        $indexOfMiddle = $update->get($update->count() / 2);
                        $subOfMiddleNumbersInOrder += $indexOfMiddle;
                    }
                }

                $this->alert('Sum of in-correctly ordered updates: '.$subOfMiddleNumbersInOrder);
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function checkRules($update)
    {
        foreach ($this->rules as $rule) {
            if ($update->contains($rule[0]) && $update->contains($rule[1])) {
                $index0 = $update->search($rule[0]);
                $index1 = $update->search($rule[1]);
                if ($index0 > $index1) {
                    return [$index0, $index1];
                }
            }
        }

        return null;
    }

    private function loadData()
    {
        $this->rules = Collect([]);
        $this->updates = Collect([]);

        $this->info('Loading data...');
        $input = $this->challenge['input'];
        $input = explode("\n", $input);

        $loadingRules = true;
        foreach ($input as $line) {
            if ($line == '') {
                $loadingRules = false;

                continue;
            }

            if ($loadingRules) {
                $this->rules->add(Collect(explode('|', $line)));
            } else {
                $this->updates->add(Collect(explode(',', $line)));
            }
        }

        $this->info('Loaded '.$this->rules->count().' rules and '.$this->updates->count().' updates');
    }
}
