<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

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

    public function handle(): void
    {
        $url = config('app.gitea_url');
        $token = config('app.gitea_token');
        $username = config('app.gitea_user');
        $repo = config('app.gitea_repo');

        $this->alert("Syncing issues for all challenges to Gitea $username/$repo");

        $challenges = config('challenges');
        $year = 2015;
        foreach ($challenges as $days) {
            $this->info("Syncing issues for $year");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$url/api/v1/repos/$username/$repo/issues?milestones=$year&state=all&type=issues&token=".$token);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
            $issues = json_decode(curl_exec($ch), true);
            unset($ch);

            foreach ($days as $day => $challenge) {
                $this->info("-> Checking for issue $year/$day");
                $dayRaw = intval($day);

                foreach ($issues as $issue) {
                    if ($issue['title'] === "Day $day - Advent of Code $year" || $issue['title'] === "Day $dayRaw - Advent of Code $year" || $issue['title'] == $challenge['info']['title']) {
                        $data = $challenge['info'];
                        $data['gitea_issue_id'] = $issue['id'];
                        file_put_contents("storage/app/$year/$day/info.json", json_encode($data));
                        continue 2;
                    }
                }

                $this->alert("Creating issue for $year/$day");

                $title = $challenge['info']['title'];
                $body = "## [Advent of Code $year](https://adventofcode.com/$year/day/$day)\n\n";

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$url/api/v1/repos/$username/$repo/issues?token=".config('app.gitea_token'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"title\":\"$title\",\"body\":\"$body\",\"assignee\":\"$username\",\"milestone\":{$this->milestons[$year]},\"project\":1,\"due_date\":\"$year-12-".str_pad($day, 2, '0', STR_PAD_LEFT).'T00:00:00Z"}');
                $result = json_decode(curl_exec($ch), true);
                unset($ch);

                $data = $challenge['info'];
                $data['gitea_issue_id'] = $result['id'];
                file_put_contents("storage/app/$year/$day/info.json", json_encode($data));
            }

            $year++;
        }
    }
}
