<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Two extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/02';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 2 :: 2015';

    private Collection $presents;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2015.02');

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

                $totalFeetOfWrappingPaper = 0;

                foreach ($this->presents as $present) {
                    $totalFeetOfWrappingPaper += 2 * $present[0] * $present[1];
                    $totalFeetOfWrappingPaper += 2 * $present[1] * $present[2];
                    $totalFeetOfWrappingPaper += 2 * $present[2] * $present[0];

                    $totalFeetOfWrappingPaper += min($present[0] * $present[1], $present[1] * $present[2], $present[2] * $present[0]);
                }

                $this->alert("The elves should order $totalFeetOfWrappingPaper square feet of wrapping paper");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $totalFeetOfRibbon = 0;

                foreach ($this->presents as $present) {
                    $totalFeetOfRibbon += 2 * min($present[0] + $present[1], $present[1] + $present[2], $present[2] + $present[0]);
                    $totalFeetOfRibbon += $present[0] * $present[1] * $present[2];
                }

                $this->alert("The elves should order $totalFeetOfRibbon feet of ribbon");
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

        $this->presents = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $tmpPresents = Collect(explode('x', $line));
            $this->presents->push($tmpPresents);
        }

        $this->info("There are {$this->presents->count()} presents to wrap");
    }
}
