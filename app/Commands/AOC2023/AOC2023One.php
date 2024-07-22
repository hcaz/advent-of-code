<?php

namespace App\Commands\AOC2023;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2023One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2023/one';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2023';

    private Collection $calibrationLines;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 1: Trebuchet?!', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        $bench = new Ubench;

        $bench->start();
        switch ($option) {
            case 0:
                $this->alert('https://adventofcode.com/2023/day/1');
                $this->info(<<<'EOL'
--- Day 1: Trebuchet?! ---

Something is wrong with global snow production, and you've been selected to take a look. The Elves have even given you a map; on it, they've used stars to mark the top fifty locations that are likely to be having problems.

You've been doing this long enough to know that to restore snow operations, you need to check all fifty stars by December 25th.

Collect stars by solving puzzles. Two puzzles will be made available on each day in the Advent calendar; the second puzzle is unlocked when you complete the first. Each puzzle grants one star. Good luck!

You try to ask why they can't just use a weather machine ("not powerful enough") and where they're even sending you ("the sky") and why your map looks mostly blank ("you sure ask a lot of questions") and hang on did you just say the sky ("of course, where do you think snow comes from") when you realize that the Elves are already loading you into a trebuchet ("please hold still, we need to strap you in").

As they're making the final adjustments, they discover that their calibration document (your puzzle input) has been amended by a very young Elf who was apparently just excited to show off her art skills. Consequently, the Elves are having trouble reading the values on the document.

The newly-improved calibration document consists of lines of text; each line originally contained a specific calibration value that the Elves now need to recover. On each line, the calibration value can be found by combining the first digit and the last digit (in that order) to form a single two-digit number.

For example:

1abc2
pqr3stu8vwx
a1b2c3d4e5f
treb7uchet
In this example, the calibration values of these four lines are 12, 38, 15, and 77. Adding these together produces 142.

Consider your entire calibration document. What is the sum of all of the calibration values?

--- Part Two ---

Your calculation isn't quite right. It looks like some of the digits are actually spelled out with letters: one, two, three, four, five, six, seven, eight, and nine also count as valid "digits".

Equipped with this new information, you now need to find the real first and last digit on each line. For example:

two1nine
eightwothree
abcone2threexyz
xtwone3four
4nineeightseven2
zoneight234
7pqrstsixteen
In this example, the calibration values are 29, 83, 13, 24, 42, 14, and 76. Adding these together produces 281.

What is the sum of all of the calibration values?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $sumOfCalibrationValues = $this->calibrationLines->map(function ($line) {
                    $cleanedLine = preg_replace('/[^0-9]/', '', $line);
                    $firstDigit = substr($cleanedLine, 0, 1);
                    $lastDigit = substr($cleanedLine, -1);

                    return intval("$firstDigit$lastDigit");
                })->sum();

                $this->info("The sum of all of the calibration values is $sumOfCalibrationValues");
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $sumOfCalibrationValues = $this->calibrationLines->map(function ($line) {
                    $firstDigit = null;
                    $lastDigit = null;
                    foreach (str_split($line) as $char) {
                        if (is_numeric($char)) {
                            if (is_null($firstDigit)) {
                                $firstDigit = $char;
                            }
                            $lastDigit = $char;
                        }
                    }

                    return intval("$firstDigit$lastDigit");
                })->sum();

                $this->info("The sum of all of the calibration values is $sumOfCalibrationValues");
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
        $this->info('Running solution for problem 1 :: 2023');
        $this->info('Loading in 2023_one_input.txt');
        $data = Storage::get('2023/one_input.txt');

        $this->calibrationLines = Collect(explode("\n", $data));

        $this->info("There are {$this->calibrationLines->count()} calibration lines to process");
    }
}
