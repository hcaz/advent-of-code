<?php

namespace App\Commands\AOC2022;

use App\Enums\AOC2022GameObjects;
use App\Enums\AOC2022GameResults;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class AOC2022Two extends Command
{
    private $dataKey = [
        'A' => AOC2022GameObjects::ROCK,
        'B' => AOC2022GameObjects::PAPER,
        'C' => AOC2022GameObjects::SCISSORS,
    ];

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'browse/2022/two';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 2 :: 2022';

    private Collection $rounds;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $option = $this->menu('Day 2: Rock Paper Scissors', [
            'View problem',
            'Run step 1',
            'Run step 2',
        ])->setExitButtonText("Back")->open();

        if(is_null($option)) {
            return;
        }

        switch($option) {
            case 0:
                $this->alert('https://adventofcode.com/2022/day/2');
                $this->info(<<<EOL
--- Day 2: Rock Paper Scissors ---

The Elves begin to set up camp on the beach. To decide whose tent gets to be closest to the snack storage, a giant Rock Paper Scissors tournament is already in progress.

Rock Paper Scissors is a game between two players. Each game contains many rounds; in each round, the players each simultaneously choose one of Rock, Paper, or Scissors using a hand shape. Then, a winner for that round is selected: Rock defeats Scissors, Scissors defeats Paper, and Paper defeats Rock. If both players choose the same shape, the round instead ends in a draw.

Appreciative of your help yesterday, one Elf gives you an encrypted strategy guide (your puzzle input) that they say will be sure to help you win. "The first column is what your opponent is going to play: A for Rock, B for Paper, and C for Scissors. The second column--" Suddenly, the Elf is called away to help with someone's tent.

The second column, you reason, must be what you should play in response: X for Rock, Y for Paper, and Z for Scissors. Winning every time would be suspicious, so the responses must have been carefully chosen.

The winner of the whole tournament is the player with the highest score. Your total score is the sum of your scores for each round. The score for a single round is the score for the shape you selected (1 for Rock, 2 for Paper, and 3 for Scissors) plus the score for the outcome of the round (0 if you lost, 3 if the round was a draw, and 6 if you won).

Since you can't be sure if the Elf is trying to help you or trick you, you should calculate the score you would get if you were to follow the strategy guide.

For example, suppose you were given the following strategy guide:

----
A Y
B X
C Z
----

This strategy guide predicts and recommends the following:
- In the first round, your opponent will choose Rock (A), and you should choose Paper (Y). This ends in a win for you with a score of 8 (2 because you chose Paper + 6 because you won).
- In the second round, your opponent will choose Paper (B), and you should choose Rock (X). This ends in a loss for you with a score of 1 (1 + 0).
- The third round is a draw with both players choosing Scissors, giving you a score of 3 + 3 = 6.

In this example, if you were to follow the strategy guide, you would get a total score of 15 (8 + 1 + 6).

What would your total score be if everything goes exactly according to your strategy guide?

--- Part Two ---
The Elf finishes helping with the tent and sneaks back over to you. "Anyway, the second column says how the round needs to end: X means you need to lose, Y means you need to end the round in a draw, and Z means you need to win. Good luck!"

The total score is still calculated in the same way, but now you need to figure out what shape to choose so the round ends as indicated. The example above now goes like this:
- In the first round, your opponent will choose Rock (A), and you need the round to end in a draw (Y), so you also choose Rock. This gives you a score of 1 + 3 = 4.
- In the second round, your opponent will choose Paper (B), and you choose Rock so you lose (X) with a score of 1 + 0 = 1.
- In the third round, you will defeat your opponent's Scissors with Rock for a score of 1 + 6 = 7.

Now that you're correctly decrypting the ultra top secret strategy guide, you would get a total score of 12.

Following the Elf's instructions for the second column, what would your total score be if everything goes exactly according to your strategy guide?
EOL);
                break;
            case 1:
                $this->info('Running step 1');
                $this->dataKey['X'] = AOC2022GameObjects::ROCK;
                $this->dataKey['Y'] = AOC2022GameObjects::PAPER;
                $this->dataKey['Z'] = AOC2022GameObjects::SCISSORS;
                $this->loadData();

                $score = 0;
                $gamesWon = 0;
                $gamesLost = 0;
                $gamesDrawn = 0;
                foreach($this->rounds as $round) {
                    $score = $score + $round['you']->score();
                    if($round['you']->beats() === $round['opponent']) {
                        $score = $score + AOC2022GameResults::WIN->score();
                        $gamesWon++;
                    } elseif($round['you']->losesTo() === $round['opponent']) {
                        $score = $score + AOC2022GameResults::LOSS->score();
                        $gamesLost++;
                    } else {
                        $score = $score + AOC2022GameResults::DRAW->score();
                        $gamesDrawn++;
                    }
                }

                $score = number_format($score);

                $this->info("You won $gamesWon games, lost $gamesLost games and drew $gamesDrawn games");
                $this->alert("Your score is $score!");
                break;
            case 2:
                $this->info('Running step 2');
                $this->dataKey['X'] = AOC2022GameResults::LOSS;
                $this->dataKey['Y'] = AOC2022GameResults::DRAW;
                $this->dataKey['Z'] = AOC2022GameResults::WIN;
                $this->loadData();

                $score = 0;
                $gamesWon = 0;
                $gamesLost = 0;
                $gamesDrawn = 0;
                foreach($this->rounds as $round) {
                    $score = $score + $round['you']->score();

                    switch($round['you']) {
                        case AOC2022GameResults::WIN:
                            $gamesWon++;
                            $score = $score + $round['opponent']->losesTo()->score();
                            break;
                        case AOC2022GameResults::LOSS:
                            $gamesLost++;
                            $score = $score + $round['opponent']->beats()->score();
                            break;
                        case AOC2022GameResults::DRAW:
                            $gamesDrawn++;
                            $score = $score + $round['opponent']->score();
                            break;
                    }
                }

                $score = number_format($score);

                $this->info("You won $gamesWon games, lost $gamesLost games and drew $gamesDrawn games");
                $this->alert("Your score is $score!");
                break;
        }

        $this->ask('Press any key to continue');
        $this->handle();
    }

    private function loadData() {
        $this->info("Running solution for problem 2 :: 2022");
        $this->info("Loading in 2022_two_input.txt");
        $data = Storage::get('2022/two_input.txt');

        $this->rounds = Collect([]);
        foreach(explode("\n", $data) as $line) {
            if($line == '') continue;

            $round = explode(' ', $line);
            if(count($round) == 2) $this->rounds->push(['opponent' => $this->dataKey[$round[0]], 'you' => $this->dataKey[$round[1]]]);
        }

        $this->info("There are {$this->rounds->count()} rounds");
    }
}
