<?php

namespace App\Commands\AOC2024;

use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2024Six extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2024/06';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 6 :: 2024';

    private array $data = [];

    private int $guardX = 0;

    private int $guardY = 0;

    private $challenge;

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->challenge = config('challenges.2024.06');

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

                $positions = $this->runGuardPath();

                $this->alert('Visited positions: '.count($positions));

                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $positions = $this->runGuardPath();

                $possibleObjectLocations = [];

                foreach (array_keys($positions) as $position) {
                    $this->loadData(silent: true);

                    $position = explode('x', $position);
                    $x = $position[0];
                    $y = $position[1];

                    if ($x == $this->guardX && $y == $this->guardY) {
                        continue;
                    }

                    $this->data[$y][$x] = '@';

                    $newPositions = $this->runGuardPath();
                    if (is_null($newPositions)) {
                        $possibleObjectLocations[] = "{$x}x{$y}";
                    }
                }

                $this->alert('Possible places to place objects and cause loops: '.count($possibleObjectLocations));
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function runGuardPath(): ?array
    {
        $positions = [];
        $positions["{$this->guardX}x{$this->guardY}"] = 1;

        while ($this->guardX > 0 && $this->guardX < (count($this->data[0]) - 1) && $this->guardY > 0 && $this->guardY < (count($this->data) - 1)) {
            $direction = $this->data[$this->guardY][$this->guardX];
            $this->data[$this->guardY][$this->guardX] = '.';

            $direction = $this->checkGuardRotation($direction);

            if ($direction === '^') {
                $this->guardY--;
            } elseif ($direction === 'v') {
                $this->guardY++;
            } elseif ($direction === '<') {
                $this->guardX--;
            } elseif ($direction === '>') {
                $this->guardX++;
            }
            $this->data[$this->guardY][$this->guardX] = $direction;

            if (! isset($positions["{$this->guardX}x{$this->guardY}"])) {
                $positions["{$this->guardX}x{$this->guardY}"] = 1;
            } else {
                $positions["{$this->guardX}x{$this->guardY}"]++;

                if ($positions["{$this->guardX}x{$this->guardY}"] > 10) {
                    return null;
                }
            }
        }

        return $positions;
    }

    private function checkGuardRotation($direction): string
    {
        $next = match ($direction) {
            '^' => $this->data[$this->guardY - 1][$this->guardX],
            'v' => $this->data[$this->guardY + 1][$this->guardX],
            '<' => $this->data[$this->guardY][$this->guardX - 1],
            '>' => $this->data[$this->guardY][$this->guardX + 1],
        };

        if ($next == '#' || $next == '@') {
            $direction = match ($direction) {
                '^' => '>',
                'v' => '<',
                '<' => '^',
                '>' => 'v',
            };

            $direction = $this->checkGuardRotation($direction);
        }

        return $direction;
    }

    private function loadData($silent = false)
    {
        $this->data = [];

        if (! $silent) {
            $this->info('Loading data...');
        }
        $input = $this->challenge['input'];
        $input = explode("\n", $input);
        foreach ($input as $lineData) {
            if (empty($lineData)) {
                continue;
            }
            $line = [];
            for ($i = 0; $i < strlen($lineData); $i++) {
                $line[] = $lineData[$i];

                if ($lineData[$i] === '^') {
                    $this->guardX = $i;
                    $this->guardY = count($this->data);
                }
            }
            $this->data[] = $line;
        }

        if (! $silent) {
            $this->info('Loaded grid of '.count($this->data).'x'.count($this->data[0]));
            $this->info('Guard position: '.$this->guardX.'x'.$this->guardY);
        }
    }
}
