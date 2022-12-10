<?php

namespace App\Commands\AOC2015;

use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2015One extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2015/one';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 1 :: 2015';

    private String $instructions;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 1: Title', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText('Back')->open();

        if (is_null($option)) {
            return;
        }

        $bench = new Ubench;

        $bench->start();
        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2015/day/1');
                $this->info(<<<'EOL'
--- Day 1: Not Quite Lisp ---
Santa was hoping for a white Christmas, but his weather machine's "snow" function is powered by stars, and he's fresh out! To save Christmas, he needs you to collect fifty stars by December 25th.

Collect stars by helping Santa solve puzzles. Two puzzles will be made available on each day in the Advent calendar; the second puzzle is unlocked when you complete the first. Each puzzle grants one star. Good luck!

Here's an easy puzzle to warm you up.

Santa is trying to deliver presents in a large apartment building, but he can't find the right floor - the directions he got are a little confusing. He starts on the ground floor (floor 0) and then follows the instructions one character at a time.

An opening parenthesis, (, means he should go up one floor, and a closing parenthesis, ), means he should go down one floor.

The apartment building is very tall, and the basement is very deep; he will never find the top or bottom floors.

For example:
- (()) and ()() both result in floor 0.
- ((( and (()(()( both result in floor 3.
- ))((((( also results in floor 3.
- ()) and ))( both result in floor -1 (the first basement level).
- ))) and )())()) both result in floor -3.

To what floor do the instructions take Santa?

--- Part Two ---
Now, given the same instructions, find the position of the first character that causes him to enter the basement (floor -1). The first character in the instructions has position 1, the second character has position 2, and so on.

For example:
- ) causes him to enter the basement at character position 1.
- ()()) causes him to enter the basement at character position 5.

What is the position of the character that causes Santa to first enter the basement?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $up = substr_count($this->instructions, '(');
                $down = substr_count($this->instructions, ')');

                $this->alert("Santa is on floor " . ($up - $down));
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $floor = 0;
                $position = 0;
                $length = strlen($this->instructions);
                while ($floor >= 0 && $position < $length) {
                    $floor += $this->instructions[$position] == '(' ? 1 : -1;
                    $position++;

                    if($floor < 0) {
                        $this->alert("Santa entered the basement at position $position");
                    }
                }
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
        $this->info('Running solution for problem 1 :: YEAR');
        $this->info('Loading in 2015_one_input.txt');
        $data = Storage::get('2015/one_input.txt');

        $this->instructions = $data;
    }
}
