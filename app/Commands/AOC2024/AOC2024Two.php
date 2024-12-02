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

                $this->processReports(0);
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $this->processReports(1);
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function processReports($allowedLevelIssues = 0)
    {
        $safeReports = 0;
        $unsafeReports = 0;

        while ($this->reports->count() > 0) {
            $report = $this->reports->pop();
            $lastNumber = null;
            $increasing = null;
            $badNumbers = 0;
            foreach ($report as $number) {
                if ($lastNumber == $number) {
                    $badNumbers++;
                } elseif (! is_null($lastNumber)) {
                    $diff = intval($lastNumber) - intval($number);

                    if (is_null($increasing)) {
                        $increasing = $diff > 0;
                    }

                    if (abs($diff) > 3) {
                        $badNumbers++;
                    } elseif (($increasing && $diff < 0) || (! $increasing && $diff > 0)) {
                        $badNumbers++;
                    }
                }

                $lastNumber = $number;
            }

            if ($badNumbers > $allowedLevelIssues) {
                $unsafeReports++;
            } else {
                $safeReports++;
            }
        }

        $this->info("Safe reports: {$safeReports}");
        $this->info("Unsafe reports: {$unsafeReports}");
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
