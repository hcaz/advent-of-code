<?php

declare(strict_types=1);

use Aoc\LeakScanner;

beforeEach(function () {
    $base = sys_get_temp_dir().'/aoc-leak-'.bin2hex(random_bytes(6));
    $this->data = $base.'/data';
    $this->build = $base.'/build';

    mkdir($this->data.'/2024/01', 0o755, true);
    mkdir($this->build, 0o755, true);

    $this->secret = 'this is a distinctive line of puzzle input 12345';
    file_put_contents(
        $this->data.'/2024/01/input.txt',
        $this->secret."\n".'another sufficiently long line of input here'."\n",
    );
});

afterEach(function () {
    exec('rm -rf '.escapeshellarg(dirname($this->data)));
});

it('derives fingerprints from readable puzzle files', function () {
    expect((new LeakScanner($this->data))->fingerprints())->not->toBeEmpty();
});

it('derives no fingerprints when the data is encrypted', function () {
    file_put_contents($this->data.'/2024/01/input.txt', "\0GITCRYPT\0".random_bytes(64));

    $scanner = new LeakScanner($this->data);

    expect($scanner->fingerprints())->toBeEmpty()
        ->and($scanner->isLocked())->toBeTrue();
});

it('reports a clean build', function () {
    file_put_contents($this->build.'/index.html', '<h1>Answers only</h1><code>2166959</code>');

    expect((new LeakScanner($this->data))->scan($this->build))->toBeEmpty();
});

it('catches puzzle content in the output', function () {
    file_put_contents($this->build.'/index.html', '<pre>'.$this->secret.'</pre>');

    $leaks = (new LeakScanner($this->data))->scan($this->build);

    expect($leaks)->toHaveCount(1)
        ->and($leaks[0]['file'])->toContain('index.html')
        ->and($leaks[0]['source'])->toContain('input.txt');
});

it('catches content nested deep in the output tree', function () {
    mkdir($this->build.'/2024/01', 0o755, true);
    file_put_contents($this->build.'/2024/01/index.html', 'prefix '.$this->secret.' suffix');

    expect((new LeakScanner($this->data))->scan($this->build))->toHaveCount(1);
});

it('ignores short lines that would false-positive', function () {
    file_put_contents($this->data.'/2024/01/input.txt', "1 2\n3 4\n5 6\n");
    file_put_contents($this->build.'/index.html', '<p>1 2</p>');

    // Those lines are far too short to be distinctive, so no fingerprint is
    // taken from them and the scan has nothing to report.
    expect((new LeakScanner($this->data))->fingerprints())->toBeEmpty();
});

it('returns no leaks for a build directory that does not exist', function () {
    expect((new LeakScanner($this->data))->scan($this->build.'/nope'))->toBeEmpty();
});

it('is not locked when the data is readable', function () {
    expect((new LeakScanner($this->data))->isLocked())->toBeFalse();
});
