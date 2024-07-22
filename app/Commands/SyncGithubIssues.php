<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class SyncGithubIssues extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync-github-issues';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync Github issues for Advent of Code solutions';

    public function handle(): void
    {
        $username = config('app.github_user');
        $repo = config('app.github_repo');

        $this->alert("Syncing issues for all challenges to Github $username/$repo");

        $challenges = config('challenges');
        $year = 2015;
        foreach ($challenges as $days) {
            $this->info("Syncing issues for $year");

            $year++;
        }
    }
}
