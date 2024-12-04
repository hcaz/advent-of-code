<?php

namespace App\Commands\AOC2022;

use App\Enums\AOC2022GameObjects;
use App\Enums\AOC2022GameResults;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

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
    protected $signature = 'challenge/2022/02';

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
        $this->challenge = config('challenges.2022.02');

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
                $this->dataKey['X'] = AOC2022GameObjects::ROCK;
                $this->dataKey['Y'] = AOC2022GameObjects::PAPER;
                $this->dataKey['Z'] = AOC2022GameObjects::SCISSORS;
                $this->loadData();

                $score = 0;
                $gamesWon = 0;
                $gamesLost = 0;
                $gamesDrawn = 0;
                foreach ($this->rounds as $round) {
                    $score = $score + $round['you']->score();
                    if ($round['you']->beats() === $round['opponent']) {
                        $score = $score + AOC2022GameResults::WIN->score();
                        $gamesWon++;
                    } elseif ($round['you']->losesTo() === $round['opponent']) {
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
                foreach ($this->rounds as $round) {
                    $score = $score + $round['you']->score();

                    switch ($round['you']) {
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

        $this->rounds = Collect([]);
        foreach (explode("\n", $data) as $line) {
            if ($line == '') {
                continue;
            }

            $round = explode(' ', $line);
            if (count($round) == 2) {
                $this->rounds->push(['opponent' => $this->dataKey[$round[0]], 'you' => $this->dataKey[$round[1]]]);
            }
        }

        $this->info("There are {$this->rounds->count()} rounds");
    }
}
