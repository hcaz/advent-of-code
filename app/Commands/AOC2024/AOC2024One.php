<?php

namespace App\Commands\AOC2024;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/01';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2024';

    private Collection $listA;

    private Collection $listB;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.01');

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

                $listA = $this->listA->sort();
                $listB = $this->listB->sort();

                $count = 0;
                while ($listA->count() > 0) {
                    $a = $listA->pop();
                    $b = $listB->pop();
                    $count += abs($a - $b);
                }

                $this->info("The total distance is: {$count}");

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $listA = $this->listA->sort();
                $listB = $this->listB->sort();

                $count = 0;
                while ($listA->count() > 0) {
                    $a = $listA->pop();
                    $times = $listB->countBy(function ($value) use ($a) {
                        return $value == $a;
                    });
                    if (isset($times[1])) {
                        $count += ($a * $times[1]);
                    }
                }

                $this->info("The total distance is: {$count}");

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
        $this->listA = Collect([]);
        $this->listB = Collect([]);

        $this->info('Loading data...');
        $input = $this->challenge['input'];
        $input = explode("\n", $input);
        foreach ($input as $line) {
            $data = explode('   ', $line);
            if (count($data) > 1) {
                $this->listA->add($data[0]);
                $this->listB->add($data[1]);
            }
        }

        $this->info("There are {$this->listA->count()}:{$this->listB->count()} locations to process");
    }
}
