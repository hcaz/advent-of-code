<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/01';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2022';

    private Collection $elves;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.01');

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

                $mostCaloriesElement = $this->elves->first();
                $mostCaloriesElf = $this->elves->search(function ($elf) use ($mostCaloriesElement) {
                    return $elf === $mostCaloriesElement;
                });
                $mostCaloriesTotal = number_format($this->elves[$mostCaloriesElf]->sum());
                $mostCaloriesItems = $this->elves[$mostCaloriesElf]->count();

                $this->alert("Elf #$mostCaloriesElf has $mostCaloriesTotal total calories across $mostCaloriesItems items");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $mostCaloriesElements = $this->elves->take(3);
                $mostCaloriesTotalTop3 = 0;
                $mostCaloriesItemsTop3 = 0;
                foreach ($mostCaloriesElements as $mostCaloriesElement) {
                    $mostCaloriesElf = $this->elves->search(function ($elf) use ($mostCaloriesElement) {
                        return $elf === $mostCaloriesElement;
                    });
                    $mostCaloriesTotal = number_format($this->elves[$mostCaloriesElf]->sum());
                    $mostCaloriesItems = $this->elves[$mostCaloriesElf]->count();

                    $mostCaloriesTotalTop3 = $mostCaloriesTotalTop3 + $this->elves[$mostCaloriesElf]->sum();
                    $mostCaloriesItemsTop3 = $mostCaloriesItemsTop3 + $mostCaloriesItems;

                    $this->info("Elf #$mostCaloriesElf has $mostCaloriesTotal total calories across $mostCaloriesItems items");
                }

                $mostCaloriesTotalTop3 = number_format($mostCaloriesTotalTop3);
                $this->alert("Top 3 elves have $mostCaloriesTotalTop3 total calories across $mostCaloriesItemsTop3 items");
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

        $this->elves = Collect([]);
        $tmpElf = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                $this->elves->push($tmpElf);
                $tmpElf = Collect([]);
            } else {
                $tmpElf->push($line);
            }
        }

        $this->info("There are {$this->elves->count()} elves");

        $this->elves = $this->elves->sortByDesc(function ($elf) {
            return $elf->sum();
        });
    }
}
