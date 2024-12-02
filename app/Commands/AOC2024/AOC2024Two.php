<?php

namespace App\Commands\AOC2024;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Two extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/02';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 2 :: 2024';

    private Collection $reports;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.02');

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

                $safeReports = 0;
                $unsafeReports = 0;

                while ($this->reports->count() > 0) {
                    $report = $this->reports->pop();
                    $lastNumber = null;
                    $increasing = null;
                    foreach ($report as $number) {
                        if (is_null($lastNumber)) {
                            $lastNumber = $number;

                            continue;
                        }

                        if ($lastNumber == $number) {
                            $unsafeReports++;

                            continue 2;
                        }
                        $diff = intval($lastNumber) - intval($number);
                        if (is_null($increasing)) {
                            if ($diff < 0) {
                                $increasing = false;
                            } else {
                                $increasing = true;
                            }
                        } elseif (($increasing && $diff < 0) || (! $increasing && $diff > 0)) {
                            $unsafeReports++;

                            continue 2;
                        }
                        if (abs($diff) > 3) {
                            $unsafeReports++;

                            continue 2;
                        }

                        $lastNumber = $number;
                    }

                    $safeReports++;
                }

                $this->info("Safe reports: {$safeReports}");
                $this->info("Unsafe reports: {$unsafeReports}");

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
        $this->reports = Collect([]);

        $this->info('Loading data...');
        $input = $this->challenge['input'];
        $input = explode("\n", $input);
        foreach ($input as $line) {
            $data = explode(' ', $line);
            if (count($data) > 0 && ! empty($data[0])) {
                $this->reports->add($data);
            }
        }

        $this->info("There are {$this->reports->count()} reports to process");
    }
}
