<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class AOC2022Five extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022/five';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 5 :: 2022';

    private Collection $stacks;
    private Collection $moves;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 5: Supply Stacks', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText("Back")->open();

        if(is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/5');
                $this->info(<<<EOL
--- Day 5: Supply Stacks ---
The expedition can depart as soon as the final supplies have been unloaded from the ships. Supplies are stored in stacks of marked crates, but because the needed supplies are buried under many other crates, the crates need to be rearranged.

The ship has a giant cargo crane capable of moving crates between stacks. To ensure none of the crates get crushed or fall over, the crane operator will rearrange them in a series of carefully-planned steps. After the crates are rearranged, the desired crates will be at the top of each stack.

The Elves don't want to interrupt the crane operator during this delicate procedure, but they forgot to ask her which crate will end up where, and they want to be ready to unload them as soon as possible so they can embark.

They do, however, have a drawing of the starting stacks of crates and the rearrangement procedure (your puzzle input). For example:

----
    [D]
[N] [C]
[Z] [M] [P]
 1   2   3

move 1 from 2 to 1
move 3 from 1 to 3
move 2 from 2 to 1
move 1 from 1 to 2
----

In this example, there are three stacks of crates. Stack 1 contains two crates: crate Z is on the bottom, and crate N is on top. Stack 2 contains three crates; from bottom to top, they are crates M, C, and D. Finally, stack 3 contains a single crate, P.

Then, the rearrangement procedure is given. In each step of the procedure, a quantity of crates is moved from one stack to a different stack. In the first step of the above rearrangement procedure, one crate is moved from stack 2 to stack 1, resulting in this configuration:

----
[D]
[N] [C]
[Z] [M] [P]
 1   2   3
----

In the second step, three crates are moved from stack 1 to stack 3. Crates are moved one at a time, so the first crate to be moved (D) ends up below the second and third crates:

----
        [Z]
        [N]
    [C] [D]
    [M] [P]
 1   2   3
----

Then, both crates are moved from stack 2 to stack 1. Again, because crates are moved one at a time, crate C ends up below crate M:

----
        [Z]
        [N]
[M]     [D]
[C]     [P]
 1   2   3
----

Finally, one crate is moved from stack 1 to stack 2:

----
        [Z]
        [N]
        [D]
[C] [M] [P]
 1   2   3
----

The Elves just need to know which crate will end up on top of each stack; in this example, the top crates are C in stack 1, M in stack 2, and Z in stack 3, so you should combine these together and give the Elves the message CMZ.

After the rearrangement procedure completes, what crate ends up on top of each stack?

--- Part Two ---
As you watch the crane operator expertly rearrange the crates, you notice the process isn't following your prediction.

Some mud was covering the writing on the side of the crane, and you quickly wipe it away. The crane isn't a CrateMover 9000 - it's a CrateMover 9001.

The CrateMover 9001 is notable for many new and exciting features: air conditioning, leather seats, an extra cup holder, and the ability to pick up and move multiple crates at once.

Again considering the example above, the crates begin in the same configuration:

----
    [D]
[N] [C]
[Z] [M] [P]
 1   2   3
----

Moving a single crate from stack 2 to stack 1 behaves the same as before:

----
[D]
[N] [C]
[Z] [M] [P]
 1   2   3
----

However, the action of moving three crates from stack 1 to stack 3 means that those three moved crates stay in the same order, resulting in this new configuration:

----
        [D]
        [N]
    [C] [Z]
    [M] [P]
 1   2   3
----

Next, as both crates are moved from stack 2 to stack 1, they retain their order as well:

----
        [D]
        [N]
[C]     [Z]
[M]     [P]
 1   2   3
----

Finally, a single crate is still moved from stack 1 to stack 2, but now it's crate C that gets moved:

----
        [D]
        [N]
        [Z]
[M] [C] [P]
 1   2   3
----

In this example, the CrateMover 9001 has put the crates in a totally different order: MCD.

Before the rearrangement process finishes, update your simulation so that the Elves know where they should stand to be ready to unload the final supplies. After the rearrangement procedure completes, what crate ends up on top of each stack?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->loadData();

                $this->info("Starting position:");
                $this->displayStacks();

                foreach($this->moves as $move) {
                    for($i = 0; $i < $move['amount']; $i++) {
                        $crate = $this->stacks[$move['from'] - 1]->pop();
                        $this->stacks[$move['to'] - 1]->push($crate);
                    }
                }

                $this->info("Ending position:");
                $this->displayStacks();

                $this->alert("Final crates at top of stacks: " . $this->stacks->map(function($stack) {
                    return $stack->pop();
                })->implode(''));
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $this->info("Starting position:");
                $this->displayStacks();

                foreach($this->moves as $move) {
                    $crates = $this->stacks[$move['from'] - 1]->pop($move['amount']);
                    foreach($crates->reverse() as $crate) {
                        $this->stacks[$move['to'] - 1]->push($crate);
                    }
                }

                $this->info("Ending position:");
                $this->displayStacks();

                $this->alert("Final crates at top of stacks: " . $this->stacks->map(function($stack) {
                        return $stack->pop();
                    })->implode(''));
                break;
        }

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData() {
        $this->info("Running solution for problem 5 :: 2022");
        $this->info("Loading in 2022_five_input.txt");
        $data = Storage::get('2022/five_input.txt');

        $this->stacks = Collect([]);
        $this->moves = Collect([]);
        $stacks = true;
        foreach(explode("\n", $data) as $line) {
            if($line == '') {
                $stacks = false;
                continue;
            }

            if($stacks) {
                if(!str_contains($line, "[")) continue;

                $stackCount = round(strlen($line) / 4);
                for($i = 0; $i < $stackCount; $i++) {
                    if(!isset($this->stacks[$i])) $this->stacks[$i] = Collect([]);
                    $crate = substr($line, ($i * 4) + 1, 1);
                    if(!empty(trim($crate))) $this->stacks[$i]->prepend($crate);
                }
            } else {
                $move = explode(' ', $line);
                $this->moves->push([
                    'amount' => $move[1],
                    'from' => $move[3],
                    'to' => $move[5],
                ]);
            }
        }

        $this->info("There are {$this->stacks->count()} stacks");
        $this->info("There are {$this->moves->count()} moves");
    }

    private function displayStacks() {
        $this->table(['Stack', 'Crate'], $this->stacks->map(function($stack, $key) {
            return [
                $key + 1,
                $stack->implode(' '),
            ];
        })->toArray());
    }
}
