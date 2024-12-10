<?php

namespace App\Commands\AOC2024;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Ten extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/10';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 10 :: 2024';

    private array $data;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.10');

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

                $sumOfTrailHeadScores = 0;
                $numberOfTrailHeads = 0;

                foreach ($this->data as $rowIndex => $line) {
                    foreach ($line as $colIndex => $char) {
                        if ($char === '0') {
                            $foundTrailEnds = $this->calculateTrailHeadScore($rowIndex, $colIndex);
                            $score = count($foundTrailEnds);
                            if ($score > 0) {
                                $numberOfTrailHeads++;
                                $sumOfTrailHeadScores += $score;
                            }
                        }
                    }
                }

                $this->alert("Number of trail heads: $numberOfTrailHeads");
                $this->alert("Sum of trail heads: $sumOfTrailHeadScores");

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $sumOfTrailHeadScores = 0;
                $numberOfTrailHeads = 0;

                foreach ($this->data as $rowIndex => $line) {
                    foreach ($line as $colIndex => $char) {
                        if ($char === '0') {
                            $foundTrailEnds = $this->calculateTrailHeadScore($rowIndex, $colIndex, ignoreDuplicateRoutes: true);
                            $score = count($foundTrailEnds);
                            if ($score > 0) {
                                $numberOfTrailHeads++;
                                $sumOfTrailHeadScores += array_sum($foundTrailEnds);
                            }
                        }
                    }
                }

                $this->alert("Number of trail heads: $numberOfTrailHeads");
                $this->alert("Sum of unique trail heads: $sumOfTrailHeadScores");
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function calculateTrailHeadScore($rowIndex, $colIndex, $value = 1, $foundTrailEnds = [], $ignoreDuplicateRoutes = false): array
    {
        if (isset($this->data[$rowIndex][$colIndex + 1])) {
            if (intval($this->data[$rowIndex][$colIndex + 1]) == 9 && $value == 9 && (! isset($foundTrailEnds["$rowIndex-".($colIndex + 1)]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds["$rowIndex-".($colIndex + 1)])) {
                    $foundTrailEnds["$rowIndex-".($colIndex + 1)] = 1;
                } else {
                    $foundTrailEnds["$rowIndex-".($colIndex + 1)]++;
                }
            } elseif (intval($this->data[$rowIndex][$colIndex + 1]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex, $colIndex + 1, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }
        if (isset($this->data[$rowIndex][$colIndex - 1])) {
            if (intval($this->data[$rowIndex][$colIndex - 1]) == 9 && $value == 9 && (! isset($foundTrailEnds["$rowIndex-".($colIndex - 1)]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds["$rowIndex-".($colIndex - 1)])) {
                    $foundTrailEnds["$rowIndex-".($colIndex - 1)] = 1;
                } else {
                    $foundTrailEnds["$rowIndex-".($colIndex - 1)]++;
                }
            } elseif (intval($this->data[$rowIndex][$colIndex - 1]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex, $colIndex - 1, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }

        if (isset($this->data[$rowIndex + 1][$colIndex])) {
            if (intval($this->data[$rowIndex + 1][$colIndex]) == 9 && $value == 9 && (! isset($foundTrailEnds[($rowIndex + 1).'-'.$colIndex]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds[($rowIndex + 1).'-'.$colIndex])) {
                    $foundTrailEnds[($rowIndex + 1).'-'.$colIndex] = 1;
                } else {
                    $foundTrailEnds[($rowIndex + 1).'-'.$colIndex]++;
                }
            } elseif (intval($this->data[$rowIndex + 1][$colIndex]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex + 1, $colIndex, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }
        if (isset($this->data[$rowIndex - 1][$colIndex])) {
            if (intval($this->data[$rowIndex - 1][$colIndex]) == 9 && $value == 9 && (! isset($foundTrailEnds[($rowIndex - 1).'-'.$colIndex]) || $ignoreDuplicateRoutes)) {
                if (! isset($foundTrailEnds[($rowIndex - 1).'-'.$colIndex])) {
                    $foundTrailEnds[($rowIndex - 1).'-'.$colIndex] = 1;
                } else {
                    $foundTrailEnds[($rowIndex - 1).'-'.$colIndex]++;
                }
            } elseif (intval($this->data[$rowIndex - 1][$colIndex]) == $value) {
                $foundTrailEnds = array_merge($foundTrailEnds, $this->calculateTrailHeadScore($rowIndex - 1, $colIndex, value: $value + 1, foundTrailEnds: $foundTrailEnds, ignoreDuplicateRoutes: $ignoreDuplicateRoutes));
            }
        }

        return $foundTrailEnds;
    }

    private function loadData()
    {
        $this->data = [];
        $this->info('Loading data...');
        $input = $this->challenge['input'];
        $input = explode("\n", $input);
        foreach ($input as $lineData) {
            if (empty($lineData)) {
                continue;
            }
            $line = [];
            for ($i = 0; $i < strlen($lineData); $i++) {
                $line[] = $lineData[$i];
            }
            $this->data[] = $line;
        }

        $this->info('Loaded grid of '.count($this->data).'x'.count($this->data[0]));
    }
}
