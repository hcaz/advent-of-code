<?php

namespace App\Commands\AOC2024;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Four extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/04';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 4 :: 2024';

    private array $data;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.04');

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

                $xmasCount = 0;
                $rightBound = count($this->data[0]) - 3;
                $downBound = count($this->data) - 3;
                $leftBound = 2;
                $upBound = 2;

                foreach ($this->data as $lineIndex => $line) {
                    foreach ($line as $charIndex => $char) {
                        if ($char == 'X') {
                            //....
                            //XMAS
                            //....
                            //....
                            if ($charIndex < $rightBound && $this->data[$lineIndex][$charIndex + 1] == 'M' && $this->data[$lineIndex][$charIndex + 2] == 'A' && $this->data[$lineIndex][$charIndex + 3] == 'S') {
                                $xmasCount++;
                            }
                            //....
                            //SMAX
                            //....
                            //....
                            if ($charIndex > $leftBound && $this->data[$lineIndex][$charIndex - 1] == 'M' && $this->data[$lineIndex][$charIndex - 2] == 'A' && $this->data[$lineIndex][$charIndex - 3] == 'S') {
                                $xmasCount++;
                            }
                            //..X.
                            //..M.
                            //..A.
                            //..S.
                            if ($lineIndex < $downBound && $this->data[$lineIndex + 1][$charIndex] == 'M' && $this->data[$lineIndex + 2][$charIndex] == 'A' && $this->data[$lineIndex + 3][$charIndex] == 'S') {
                                $xmasCount++;
                            }
                            //..S.
                            //..A.
                            //..M.
                            //..X.
                            if ($lineIndex > $upBound && $this->data[$lineIndex - 1][$charIndex] == 'M' && $this->data[$lineIndex - 2][$charIndex] == 'A' && $this->data[$lineIndex - 3][$charIndex] == 'S') {
                                $xmasCount++;
                            }
                            //...X
                            //..M.
                            //.A..
                            //S...
                            if ($lineIndex < $downBound && $charIndex < $rightBound && $this->data[$lineIndex + 1][$charIndex + 1] == 'M' && $this->data[$lineIndex + 2][$charIndex + 2] == 'A' && $this->data[$lineIndex + 3][$charIndex + 3] == 'S') {
                                $xmasCount++;
                            }
                            //X...
                            //.M..
                            //..A.
                            //...S
                            if ($lineIndex < $downBound && $charIndex > $leftBound && $this->data[$lineIndex + 1][$charIndex - 1] == 'M' && $this->data[$lineIndex + 2][$charIndex - 2] == 'A' && $this->data[$lineIndex + 3][$charIndex - 3] == 'S') {
                                $xmasCount++;
                            }
                            //...S
                            //..A.
                            //.M..
                            //X...
                            if ($lineIndex > $upBound && $charIndex < $rightBound && $this->data[$lineIndex - 1][$charIndex + 1] == 'M' && $this->data[$lineIndex - 2][$charIndex + 2] == 'A' && $this->data[$lineIndex - 3][$charIndex + 3] == 'S') {
                                $xmasCount++;
                            }
                            //S...
                            //.A..
                            //..M.
                            //...X
                            if ($lineIndex > $upBound && $charIndex > $leftBound && $this->data[$lineIndex - 1][$charIndex - 1] == 'M' && $this->data[$lineIndex - 2][$charIndex - 2] == 'A' && $this->data[$lineIndex - 3][$charIndex - 3] == 'S') {
                                $xmasCount++;
                            }
                        }
                    }
                }

                $this->alert('XMAS count: '.$xmasCount);

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $x_masCount = 0;
                $rightBound = count($this->data[0]) - 1;
                $downBound = count($this->data) - 1;
                $leftBound = 0;
                $upBound = 0;

                foreach ($this->data as $lineIndex => $line) {
                    foreach ($line as $charIndex => $char) {
                        if ($charIndex < $rightBound && $charIndex > $leftBound && $lineIndex < $downBound && $lineIndex > $upBound && $char == 'A') {
                            //M.S
                            //.A.
                            //M.S
                            if ($this->data[$lineIndex - 1][$charIndex - 1] == 'M' &&
                                $this->data[$lineIndex + 1][$charIndex + 1] == 'S' &&
                                $this->data[$lineIndex + 1][$charIndex - 1] == 'M' &&
                                $this->data[$lineIndex - 1][$charIndex + 1] == 'S') {
                                $x_masCount++;
                            }
                            //S.M
                            //.A.
                            //S.M
                            if ($this->data[$lineIndex + 1][$charIndex + 1] == 'M' &&
                                $this->data[$lineIndex - 1][$charIndex - 1] == 'S' &&
                                $this->data[$lineIndex - 1][$charIndex + 1] == 'M' &&
                                $this->data[$lineIndex + 1][$charIndex - 1] == 'S') {
                                $x_masCount++;
                            }
                            //M.M
                            //.A.
                            //S.S
                            if ($this->data[$lineIndex - 1][$charIndex - 1] == 'M' &&
                                $this->data[$lineIndex + 1][$charIndex + 1] == 'S' &&
                                $this->data[$lineIndex - 1][$charIndex + 1] == 'M' &&
                                $this->data[$lineIndex + 1][$charIndex - 1] == 'S') {
                                $x_masCount++;
                            }
                            //S.S
                            //.A.
                            //M.M
                            if ($this->data[$lineIndex + 1][$charIndex + 1] == 'M' &&
                                $this->data[$lineIndex - 1][$charIndex - 1] == 'S' &&
                                $this->data[$lineIndex + 1][$charIndex - 1] == 'M' &&
                                $this->data[$lineIndex - 1][$charIndex + 1] == 'S') {
                                $x_masCount++;
                            }
                        }
                    }
                }

                $this->alert('X-MAS count: '.$x_masCount);
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
