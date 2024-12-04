<?php

namespace App\Commands\AOC2015;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Four extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/04';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 4 :: 2015';

    private string $secretKey;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2015.04');

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

                $i = 0;
                while (true) {
                    $hash = md5($this->secretKey.$i);
                    if (str_starts_with($hash, '00000')) {
                        $this->alert("Number $i produces hash $hash");
                        break;
                    }
                    $i++;
                }
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $i = 0;
                while (true) {
                    $hash = md5($this->secretKey.$i);
                    if (str_starts_with($hash, '000000')) {
                        $this->alert("Number $i produces hash $hash");
                        break;
                    }
                    $i++;
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
        $this->secretKey = trim($this->challenge['input']);

        $this->info("Secret key loaded: {$this->secretKey}");
    }
}
