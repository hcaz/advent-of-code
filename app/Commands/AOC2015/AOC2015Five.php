<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Five extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/05';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 5 :: 2015';

    private Collection $strings;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2015.05');

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

                $niceStrings = 0;
                foreach ($this->strings as $string) {
                    if (preg_match('/(ab|cd|pq|xy)/', $string)) {
                        continue;
                    }

                    if (preg_match('/([aeiou].*){3,}/', $string) && preg_match('/(.)\1/', $string)) {
                        $niceStrings++;
                    }
                }

                $this->alert("Nice strings: $niceStrings");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $niceStrings = 0;
                foreach ($this->strings as $string) {
                    if (preg_match('/(..).*\1/', $string) && preg_match('/(.).\1/', $string)) {
                        $niceStrings++;
                    }
                }

                $this->alert("Nice strings: $niceStrings");
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
        $this->strings = collect(explode("\n", $data));

        $this->info('Loaded '.$this->strings->count().' strings to process');
    }
}
