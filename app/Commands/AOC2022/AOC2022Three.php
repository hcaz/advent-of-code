<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Three extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/03';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 3 :: 2022';

    private Collection $rucksacks;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.03');

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

                $priorityTotal = 0;
                foreach ($this->rucksacks as $rucksack) {
                    $itemsInBothCompartments = implode(array_unique(array_intersect(str_split($rucksack['compartment_1']), str_split($rucksack['compartment_2']))));

                    // I had hoped I could change 96 to 64 depending on capital or lowercase, but that didn't work
                    $number = (ord(strtolower($itemsInBothCompartments)) - 96) + ($itemsInBothCompartments == strtoupper($itemsInBothCompartments) ? 26 : 0);
                    if ($number > 0) {
                        $priorityTotal = $priorityTotal + $number;
                    }
                }

                $priorityTotal = number_format($priorityTotal);

                $this->alert("The total priority is $priorityTotal");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $chunks = $this->rucksacks->chunk(3);

                $priorityTotal = 0;
                foreach ($chunks as $chunk) {
                    $elfOneRucksack = $chunk->values()[0]['compartment_1'].$chunk->values()[0]['compartment_2'];
                    $elfTwoRucksack = $chunk->values()[1]['compartment_1'].$chunk->values()[1]['compartment_2'];
                    $elfThreeRucksack = $chunk->values()[2]['compartment_1'].$chunk->values()[2]['compartment_2'];

                    $itemsOnAllElves = implode(array_unique(array_intersect(str_split($elfOneRucksack), str_split($elfTwoRucksack), str_split($elfThreeRucksack))));

                    // I had hoped I could change 96 to 64 depending on capital or lowercase, but that didn't work
                    $number = (ord(strtolower($itemsOnAllElves)) - 96) + ($itemsOnAllElves == strtoupper($itemsOnAllElves) ? 26 : 0);
                    if ($number > 0) {
                        $priorityTotal = $priorityTotal + $number;
                    }
                }

                $priorityTotal = number_format($priorityTotal);

                $this->alert("The total priority is $priorityTotal");
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

        $this->rucksacks = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $first_half = substr($line, 0, strlen($line) / 2);
            $second_half = substr($line, strlen($line) / 2);

            $this->rucksacks->push([
                'compartment_1' => $first_half,
                'compartment_2' => $second_half,
            ]);
        }

        $this->info("There are {$this->rucksacks->count()} rucksacks");
    }
}
