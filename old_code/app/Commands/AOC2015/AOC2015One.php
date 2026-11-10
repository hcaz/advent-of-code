<?php

namespace App\Commands\AOC2015;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/01';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2015';

    private string $instructions;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2015.01');

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

                $up = substr_count($this->instructions, '(');
                $down = substr_count($this->instructions, ')');

                $this->alert('Santa is on floor '.($up - $down));
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $floor = 0;
                $position = 0;
                $length = strlen($this->instructions);
                while ($floor >= 0 && $position < $length) {
                    $floor += $this->instructions[$position] == '(' ? 1 : -1;
                    $position++;

                    if ($floor < 0) {
                        $this->alert("Santa entered the basement at position $position");
                    }
                }
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
        $this->instructions = $this->challenge['input'];
    }
}
