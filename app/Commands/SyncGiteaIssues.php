<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class SyncGiteaIssues extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync-gitea-issues';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync Gitea issues for Advent of Code solutions';

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

                $title = $challenge['info']['title'];
                $link = $challenge['info']['link'];
                $state = 'open';
                $labels = [];
                $body = "# [$title]($link)\n\n## Step 1:\n\n{$challenge['step_one']}\n\n## Step 2:\n\n{$challenge['step_two']}";

                if (! empty($challenge['info']['step_one_answer']) && ! empty($challenge['info']['step_two_answer'])) {
                    $state = 'closed';
                    $labels[] = 'Status/★★';
                } elseif (! empty($challenge['info']['step_one_answer']) || ! empty($challenge['info']['step_two_answer'])) {
                    $labels[] = 'Status/★';
                }

                if (isset($challenge['info']['step_one_answer'])) {
                    $body .= "\n\n## Answers\n\n- Step 1: `{$challenge['info']['step_one_answer']}`";
                }
                if (isset($challenge['info']['step_two_answer'])) {
                    $body .= "\n- Step 2: `{$challenge['info']['step_two_answer']}`";
                }

                foreach ($issues as $issue) {
                    if ((isset($challenge['info']['gitea_issue_id']) && $issue['id'] == $challenge['info']['gitea_issue_id']) || $issue['title'] == $title || $issue['title'] === "Day $day - Advent of Code $year" || $issue['title'] === "Day $dayRaw - Advent of Code $year") {
                        if (! isset($challenge['info']['gitea_issue_id']) || $issue['id'] != $challenge['info']['gitea_issue_id']) {
                            $this->info("--> Storing gitea issue id for $year/$day");
                            $data = $challenge['info'];
                            $data['gitea_issue_id'] = $issue['id'];
                            file_put_contents("storage/app/$year/$day/info.json", json_encode($data));
                        }

                        $updates = [];

                        if ($issue['title'] != $title) {
                            $updates['title'] = $title;
                        }

                        if ($issue['body'] != $body) {
                            $updates['body'] = $body;
                        }

                        if ($issue['state'] != $state) {
                            $updates['state'] = $state;
                        }

                        if (count($updates) > 0) {
                            $this->info("--> Updating issue for $year/$day");
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "$url/api/v1/repos/$username/$repo/issues/{$issue['id']}?token=".config('app.gitea_token'));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updates));
                            curl_exec($ch);
                            unset($ch);

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "$url/api/v1/repos/$username/$repo/issues/{$issue['id']}/labels?token=".config('app.gitea_token'));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Content-Type: application/json']);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['labels' => $labels]));
                            curl_exec($ch);
                            unset($ch);
                        }

                        continue 2;
                    }
                }

                $this->alert("Creating issue for $year/$day");

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
