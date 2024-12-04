<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015Three extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2015/03';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 3 :: 2015';

    private Collection $directions;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2015.03');

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

                $x = 0;
                $y = 0;
                $houses = ["{$x},{$y}" => 1];

                foreach ($this->directions as $direction) {
                    switch ($direction) {
                        case '^':
                            $y++;
                            break;
                        case 'v':
                            $y--;
                            break;
                        case '>':
                            $x++;
                            break;
                        case '<':
                            $x--;
                            break;
                    }
                    if (isset($houses["{$x},{$y}"])) {
                        $houses["{$x},{$y}"]++;
                    } else {
                        $houses["{$x},{$y}"] = 1;
                    }
                }

                $houses = collect($houses);

                $this->info("Houses visited: {$houses->count()}");
                $housesWithMoreThanOneVisit = $houses->filter(fn ($visits) => $visits >= 1);

                $this->alert("Houses with more than one visit: {$housesWithMoreThanOneVisit->count()}");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $santaX = 0;
                $santaY = 0;
                $robotX = 0;
                $robotY = 0;
                $houses = ["{$santaX},{$santaY}" => 2];

                foreach ($this->directions as $index => $direction) {
                    if($index % 2 === 0) {
                        switch ($direction) {
                            case '^':
                                $santaY++;
                                break;
                            case 'v':
                                $santaY--;
                                break;
                            case '>':
                                $santaX++;
                                break;
                            case '<':
                                $santaX--;
                                break;
                        }
                        if (isset($houses["{$santaX},{$santaY}"])) {
                            $houses["{$santaX},{$santaY}"]++;
                        } else {
                            $houses["{$santaX},{$santaY}"] = 1;
                        }
                    } else {
                        switch ($direction) {
                            case '^':
                                $robotY++;
                                break;
                            case 'v':
                                $robotY--;
                                break;
                            case '>':
                                $robotX++;
                                break;
                            case '<':
                                $robotX--;
                                break;
                        }
                        if (isset($houses["{$robotX},{$robotY}"])) {
                            $houses["{$robotX},{$robotY}"]++;
                        } else {
                            $houses["{$robotX},{$robotY}"] = 1;
                        }
                    }
                }

                $houses = collect($houses);

                $this->info("Houses visited: {$houses->count()}");
                $housesWithMoreThanOneVisit = $houses->filter(fn ($visits) => $visits >= 1);

                $this->alert("Houses with more than one visit: {$housesWithMoreThanOneVisit->count()}");
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
        $data = $this->challenge['input'];

        $this->directions = collect(str_split($data));

        $this->info("There are {$this->directions->count()} directions");
    }
}
