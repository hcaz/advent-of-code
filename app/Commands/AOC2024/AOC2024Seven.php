<?php

namespace App\Commands\AOC2024;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Seven extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/07';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 7 :: 2024';

    private Collection $testResults;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.07');

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

                $sumOfValidTestResults = 0;
                $operators = ['+', '*'];

                foreach ($this->testResults as $testResult) {
                    $testValue = $testResult[0];
                    $numbersToCombine = [];
                    for ($i = 0; $i < count($testResult[1]); $i++) {
                        if($i!=0) {
                            $numbersToCombine[] = $operators;
                        }
                        $numbersToCombine[] = $testResult[1];
                    }

                    $combinations = $this->combinations($numbersToCombine);
                    foreach ($combinations as $combination) {
                        $expression = implode(' ', $combination);
                        $result = eval('$(('.$expression.'));');
                        dd($result);
//                                                try {
//                                                    $result = eval($expression);
//                                                    dd($result);
//                                                } catch (\Throwable $e) {
//                                                    $valid = false;
//                                                }
//
//                                                if ($valid && $result == $testValue) {
//                                                    $sumOfValidTestResults += $testValue;
//                                                    break;
//                                                }
                    }
                    die;

                }

                $this->alert("Valid test results sum: $sumOfValidTestResults");
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

    public function combinations(array $arrays): iterable
    {
        if ($arrays === []) {
            yield [];

            return;
        }

        $head = array_shift($arrays);

        foreach ($head as $elem) {
            foreach (self::combinations($arrays) as $combination) {
                yield [$elem, ...$combination];
            }
        }
    }

    private function loadData()
    {
        $this->testResults = Collect([]);

        $this->info('Loading data...');
        $input = $this->challenge['input'];
        $input = explode("\n", $input);

        foreach ($input as $line) {
            if ($line == '') {
                continue;
            }

            $result = explode(':', $line);
            $values = explode(' ', $result[1]);
            $values = array_filter($values, fn ($value) => $value != '');

            $this->testResults->add([$result[0], $values]);
        }

        $this->info('Loaded '.$this->testResults->count().' test results');
    }
}
