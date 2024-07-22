<?php

namespace App\Commands;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class ProblemExample extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/year/problem';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem # :: YEAR';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day #: Title', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        $bench = new Ubench;

        $bench->start();
        switch ($option) {
            case 0:
                $this->alert('https://adventofcode.com/YEAR/day/PROBLEM');
                $this->info(<<<'EOL'

EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();
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
        $this->info('Running solution for problem # :: YEAR');
        $this->info('Loading in 2022_four_input.txt');
        $data = Storage::get('2022/four_input.txt');
    }
}
