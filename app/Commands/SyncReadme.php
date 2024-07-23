<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class SyncReadme extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync-readme';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync completed challenges to the README.md file';

    public function handle(): void
    {
        $this->alert('Syncing issues for all challenges to README.md');

        $challenges = config('challenges');
        $challenge_data = '';

        $year = 2015;
        foreach ($challenges as $days) {
            $this->info("Syncing issues for $year");

            $challenge_data .= "\n### $year\n";
            foreach ($days as $day => $challenge) {
                $title = $challenge['info']['title'];
                $link = $challenge['info']['link'];

                if (! empty($challenge['info']['step_one_answer']) && ! empty($challenge['info']['step_two_answer'])) {
                    $challenge_data .= "- [$title]($link) ★★ - `php adventofcode challenge/2015/one`\n";
                } elseif (! empty($challenge['info']['step_one_answer']) || ! empty($challenge['info']['step_two_answer'])) {
                    $challenge_data .= "- [$title]($link) ★ - `php adventofcode challenge/2015/one`\n";
                }


            }

            $year++;
        }

        $readme = file_get_contents(base_path('readme_template.md'));
        $readme = str_replace('<!-- CHALLENGE_DATA -->', $challenge_data, $readme);
        file_put_contents(base_path('README.md'), $readme);
    }
}
