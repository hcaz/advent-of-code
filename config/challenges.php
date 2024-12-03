<?php

$data = [];

for ($year = 2015; $year <= date('Y'); $year++) {
    file_exists("storage/app/$year") || mkdir("storage/app/$year");

    $data[$year] = [];

    for ($day = 1; $day <= ($year == date('Y') ? (date('m') == 12 ? (date('j') <= 25 ? date('j') : 25) : 0) : 25); $day++) {
        $dayPad = str_pad($day, 2, '0', STR_PAD_LEFT);
        file_exists("storage/app/$year/$dayPad") || mkdir("storage/app/$year/$dayPad");
        if (! file_exists("storage/app/$year/$dayPad/info.json")) {
            file_put_contents("storage/app/$year/$dayPad/info.json", json_encode([
                'title' => "Day $dayPad:",
                'link' => "https://adventofcode.com/$year/day/$day",
                'step_one_answer' => '',
                'step_two_answer' => '',
            ]));
        }
        if (! file_exists("storage/app/$year/$dayPad/step_one.md")) {
            file_put_contents("storage/app/$year/$dayPad/step_one.md", '');
        }
        if (! file_exists("storage/app/$year/$dayPad/step_two.md")) {
            file_put_contents("storage/app/$year/$dayPad/step_two.md", '');
        }
        if (! file_exists("storage/app/$year/$dayPad/input.txt")) {
            file_put_contents("storage/app/$year/$dayPad/input.txt", '');
        }

        $info = file_get_contents("storage/app/$year/$dayPad/info.json");
        $step_one = file_get_contents("storage/app/$year/$dayPad/step_one.md");
        $step_two = file_get_contents("storage/app/$year/$dayPad/step_two.md");
        $input = file_get_contents("storage/app/$year/$dayPad/input.txt");

        $data[$year][$dayPad] = [
            'info' => json_decode($info, true),
            'step_one' => $step_one,
            'step_two' => $step_two,
            'input' => $input,
        ];
    }
}

return $data;
