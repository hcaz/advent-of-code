<?php

namespace App\Commands\AOC2024;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Three extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/03';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 3 :: 2024';

    private string $memory;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.03');

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

                $count = $this->calculateMuls($this->memory);

                $this->info("The result is: $count");

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                /// Add a trailing do() to ensure the last don't() is removed
                $cleanedMemory = preg_replace('/don\'t\(\)(.|\n)*?do\(\)/', '', $this->memory.'do()');
                $count = $this->calculateMuls($cleanedMemory);

                $this->info("The result is: $count");
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function calculateMuls($memory)
    {
        preg_match_all("/mul\(\d{1,3},\d{1,3}\)/", $memory, $matches);

        $count = 0;
        foreach ($matches[0] as $match) {
            $numbers = explode(',', str_replace(['mul(', ')'], '', $match));
            $count += $numbers[0] * $numbers[1];
        }

        return $count;
    }

    private function loadData()
    {
        $this->reports = Collect([]);

        $this->info('Loading data...');
        $this->memory = $this->challenge['input'];
    }
}
