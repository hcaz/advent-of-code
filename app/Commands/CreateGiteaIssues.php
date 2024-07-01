<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;
use OwenVoke\Gitea\Api\Issue;
use OwenVoke\Gitea\Client;
use OwenVoke\Gitea\Exception\MissingArgumentException;

class CreateGiteaIssues extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'create-gitea-issues';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create Gitea issues for Advent of Code solutions';

    private $milestons = [
        '2015' => 3,
        '2016' => 4,
        '2017' => 5,
        '2018' => 6,
        '2019' => 7,
        '2020' => 8,
        '2021' => 9,
        '2022' => 10,
        '2023' => 11,
        '2024' => 12,
    ];

    /**
     * Execute the console command.
     *
     * @return void
     * @throws MissingArgumentException
     */
    public function handle(): void
    {
        $url = $this->ask('Enter the URL of the Gitea instance to create issues on', 'https://enhostcode.com');
        $username = $this->ask('Enter the username of the Gitea account to create issues with', 'hcaz');
        $repo = $this->ask('Enter the repository name to create issues in', 'advent-of-code');
        $year = $this->ask('Enter the year of Advent of Code to create Gitea issues for', 2015);

        if (is_null($year)) {
            $this->info('You have chosen to exit');

            return;
        }

        if($year < 2015 || $year > 2023) {
            $this->error('Invalid year entered. Please enter a year between 2015 and 2023');

            return;
        }
        $this->info("You have chosen to create Gitea issues for Advent of Code $year");

        $client = new Client(null, null, $url);
        $client->authenticate(config('app.gitea_token'), null, Client::AUTH_ACCESS_TOKEN);
        $issues = $client->issues()->all($username, $repo, ['state' => 'all']);

        for($day = 1; $day <= 25; $day++) {
            $title = "Day $day - Advent of Code $year";
            $body = "Create a solution for Day $day of Advent of Code $year\nhttps://adventofcode.com/$year/$day";
            $issueExists = false;

            foreach($issues as $issue) {
                if($issue['title'] === $title) {
                    $issueExists = true;
                    break;
                }
            }

            if(!$issueExists) {
                $this->info("Creating issue $title");

                $client->issues()->create($username, $repo, [
                    'title' => $title,
                    'body' => $body,
                    'assignee' => $username,
                    'milestone' => $this->milestons[$year],
                    'due_date' => $year . '-12-'.str_pad($day,2, "0", STR_PAD_LEFT).'T00:00:00Z',
                ]);
            }
        }
    }
}
