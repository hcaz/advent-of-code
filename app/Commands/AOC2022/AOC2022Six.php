<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Six extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022/six';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 6 :: 2022';

    private String $buffer;
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 6: Title', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText("Back")->open();

        if(is_null($option)) {
            return;
        }

        $bench = new Ubench;

        $bench->start();
        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/6');
                $this->info(<<<EOL
--- Day 6: Tuning Trouble ---
The preparations are finally complete; you and the Elves leave camp on foot and begin to make your way toward the star fruit grove.

As you move through the dense undergrowth, one of the Elves gives you a handheld device. He says that it has many fancy features, but the most important one to set up right now is the communication system.

However, because he's heard you have significant experience dealing with signal-based systems, he convinced the other Elves that it would be okay to give you their one malfunctioning device - surely you'll have no problem fixing it.

As if inspired by comedic timing, the device emits a few colorful sparks.

To be able to communicate with the Elves, the device needs to lock on to their signal. The signal is a series of seemingly-random characters that the device receives one at a time.

To fix the communication system, you need to add a subroutine to the device that detects a start-of-packet marker in the datastream. In the protocol being used by the Elves, the start of a packet is indicated by a sequence of four characters that are all different.

The device will send your subroutine a datastream buffer (your puzzle input); your subroutine needs to identify the first position where the four most recently received characters were all different. Specifically, it needs to report the number of characters from the beginning of the buffer to the end of the first such four-character marker.

For example, suppose you receive the following datastream buffer:

----
mjqjpqmgbljsphdztnvjfqwrcgsmlb
----

After the first three characters (mjq) have been received, there haven't been enough characters received yet to find the marker. The first time a marker could occur is after the fourth character is received, making the most recent four characters mjqj. Because j is repeated, this isn't a marker.

The first time a marker appears is after the seventh character arrives. Once it does, the last four characters received are jpqm, which are all different. In this case, your subroutine should report the value 7, because the first start-of-packet marker is complete after 7 characters have been processed.

Here are a few more examples:
- bvwbjplbgvbhsrlpgdmjqwftvncz: first marker after character 5
- nppdvjthqldpwncqszvftbrmjlhg: first marker after character 6
- nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg: first marker after character 10
- zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw: first marker after character 11

How many characters need to be processed before the first start-of-packet marker is detected?

--- Part Two ---
Your device's communication system is correctly detecting packets, but still isn't working. It looks like it also needs to look for messages.

A start-of-message marker is just like a start-of-packet marker, except it consists of 14 distinct characters rather than 4.

Here are the first positions of start-of-message markers for all of the above examples:
- mjqjpqmgbljsphdztnvjfqwrcgsmlb: first marker after character 19
- bvwbjplbgvbhsrlpgdmjqwftvncz: first marker after character 23
- nppdvjthqldpwncqszvftbrmjlhg: first marker after character 23
- nznrnfrfntjfmvfwmzdfjlvtqnbhcprsg: first marker after character 29
- zcfzfwzzqfrljwzlrfnpqdbhtmscgvjw: first marker after character 26

How many characters need to be processed before the first start-of-message marker is detected?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $indexOfFirstMarker = null;

                $localBuffer = Collect([]);
                for($i = 0; $i < strlen($this->buffer); $i++) {
                    $char = $this->buffer[$i];
                    if(empty(trim($char))) continue;

                    $localBuffer->push($char);
                    if($localBuffer->count() > 4) $localBuffer->pull(0);
                    $localBuffer = $localBuffer->values();

                    if($localBuffer->count() == 4 && $localBuffer->unique()->count() == 4) {
//                        $this->info("Found 4 unique characters in a row {$localBuffer->implode('')} at index " . ($i + 1));

                        if(is_null($indexOfFirstMarker)) $indexOfFirstMarker = ($i + 1);
                    }
                }

                if(is_null($indexOfFirstMarker)) {
                    $this->error("No start-of-packet found");
                } else {
                    $this->info("Found start-of-packet found at index $indexOfFirstMarker");
                }
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $indexOfFirstMarker = null;

                $localBuffer = Collect([]);
                for($i = 0; $i < strlen($this->buffer); $i++) {
                    $char = $this->buffer[$i];
                    if(empty(trim($char))) continue;

                    $localBuffer->push($char);
                    if($localBuffer->count() > 14) $localBuffer->pull(0);
                    $localBuffer = $localBuffer->values();

                    if($localBuffer->count() == 14 && $localBuffer->unique()->count() == 14) {
//                        $this->info("Found 14 unique characters in a row {$localBuffer->implode('')} at index " . ($i + 1));

                        if(is_null($indexOfFirstMarker)) $indexOfFirstMarker = ($i + 1);
                    }
                }

                if(is_null($indexOfFirstMarker)) {
                    $this->error("No start-of-packet found");
                } else {
                    $this->info("Found start-of-packet found at index $indexOfFirstMarker");
                }
                break;
        }
        $bench->end();

        $this->error("Execution time: " . $bench->getTime());
        $this->error("Memory usage: " . $bench->getMemoryPeak());

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData() {
        $this->info("Running solution for problem 6 :: 2022");
        $this->info("Loading in 2022_six_input.txt");
        $data = Storage::get('2022/six_input.txt');

        $this->buffer = $data;
    }
}
