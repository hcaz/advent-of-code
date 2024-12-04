<?php

namespace App\Commands\AOC2022;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use Ubench;

class AOC2022Seven extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'challenge/2022/07';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Display solution for problem 7 :: 2022';

    private Collection $commands;

    private Collection $directories;

    private int $totalSize = 0;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->challenge = config('challenges.2022.07');

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
                $this->loadData();

                $this->parseDirectories();
                $this->calculateSizeOfFoldersUnderLimit();

                $this->alert('Total size of directories with a total size of at most 100000: '.$this->totalSize);
                break;
            case 2:
                $this->info('Running step 2');
                $this->loadData();

                $this->parseDirectories();
                $this->calculateSizeOfFoldersToBeRemoved();

                $this->alert('Total size of directories to be deleted: '.$this->totalSize);
                break;
        }
        $bench->end();

        $this->error("Execution time: {$bench->getTime()}");
        $this->error("Peak memory usage: {$bench->getMemoryPeak()}");

        $this->ask('Press any key to continue');
        $this->handle();
    }

    public function parseDirectories()
    {
        $directories = Collect([]);
        $currentDirectoryPrefix = '';
        foreach ($this->commands as $command) {
            if (str_contains($command, '$')) {
                $command = explode(' ', str_replace('$ ', '', $command));
                if ($command[0] == 'cd') {
                    $command[1] = ltrim($command[1], '/');

                    if ($command[1] == '..') {
                        $currentDirectoryPrefix = substr($currentDirectoryPrefix, 0, strrpos($currentDirectoryPrefix, '.'));

                        continue;
                    }

                    $directories[$currentDirectoryPrefix.$command[1]] = [];

                    $currentDirectoryPrefix = ltrim($currentDirectoryPrefix.$command[1].'.', '.');
                }
            } else {
                $command = explode(' ', $command);
                if ($command[0] != 'dir' && count($command) == 2) {
                    ///Replace file extension dot with a underscore so I can use undot later
                    $command[1] = str_replace('.', '_', $command[1]);
                    $directories[$currentDirectoryPrefix.$command[1]] = $command[0];
                }
            }
        }

        $this->directories = $directories->undot();
    }

    private function calculateSizeOfFoldersUnderLimit($children = null, $limit = 100000): int
    {
        $size = 0;

        if (is_null($children)) {
            $children = $this->directories->toArray();
        }

        if (is_numeric($children)) {
            $size += $children;
        } elseif (is_array($children)) {
            foreach ($children as $childChildren) {
                $size += $this->calculateSizeOfFoldersUnderLimit($childChildren, $limit);
            }
            if ($size <= $limit) {
                $this->totalSize += $size;
            }
        }

        return $size;
    }

    private function calculateSizeOfFoldersToBeRemoved($children = null): int
    {
        $totalDiskSpace = 70000000;
        $spaceNeeded = 30000000;
        $spaceUsed = $this->calculateSizeOfFoldersUnderLimit(PHP_INT_MAX);

        dump("TotalDiskSpace $totalDiskSpace");
        dump("SpaceNeeded $spaceNeeded");
        dump("SpaceUsed $spaceUsed");
        $spaceNeeded = $totalDiskSpace - $spaceUsed;
        dd("SpaceNeeded $spaceNeeded");

        $size = 0;

        if (is_null($children)) {
            $children = $this->directories->toArray();
        }

        if (is_numeric($children)) {
            $size += $children;
        } elseif (is_array($children)) {
            foreach ($children as $childChildren) {
                $size += $this->calculateSize($childChildren);
            }
            if ($size <= 100000) {
                $this->totalSize += $size;
            }
        }

        return $size;
    }

    private function loadData()
    {
        $this->info('Loading data...');
        $data = $this->challenge['input'];

        $this->commands = collect(explode("\n", $data));

        $this->info("There are {$this->commands->count()} commands");
    }
}
