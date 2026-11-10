<?php

declare(strict_types=1);

use Aoc\Data;

/**
 * Data is the gate that keeps puzzle inputs and puzzle prose out of the built
 * site and, by extension, out of the public mirrors. The property under test is
 * that it fails CLOSED: anything other than an explicit opt-in yields null.
 */
beforeEach(function () {
    $this->root = sys_get_temp_dir().'/aoc-data-'.bin2hex(random_bytes(6));
    mkdir($this->root.'/2024/01', 0o755, true);

    file_put_contents($this->root.'/2024/01/input.txt', "1   2\n3   4\n");
    file_put_contents($this->root.'/2024/01/step_one.md', 'Part one prose.');
    file_put_contents($this->root.'/2024/01/step_two.md', 'Part two prose.');
});

afterEach(function () {
    foreach (glob($this->root.'/2024/01/*') ?: [] as $f) {
        unlink($f);
    }
    @rmdir($this->root.'/2024/01');
    @rmdir($this->root.'/2024');
    @rmdir($this->root);
});

it('withholds everything by default', function () {
    $data = new Data($this->root);

    expect($data->input(2024, 1))->toBeNull()
        ->and($data->stepOne(2024, 1))->toBeNull()
        ->and($data->stepTwo(2024, 1))->toBeNull()
        ->and($data->inputBytes(2024, 1))->toBeNull();
});

it('returns content only when explicitly unlocked', function () {
    $data = Data::unlocked($this->root);
    $input = "1   2\n3   4\n";

    expect($data->input(2024, 1))->toBe($input)
        ->and($data->stepOne(2024, 1))->toBe('Part one prose.')
        ->and($data->stepTwo(2024, 1))->toBe('Part two prose.')
        ->and($data->inputBytes(2024, 1))->toBe(strlen($input));
});

it('reports file existence regardless of the gate, since that leaks nothing', function () {
    foreach ([new Data($this->root), Data::unlocked($this->root)] as $data) {
        expect($data->hasInput(2024, 1))->toBeTrue()
            ->and($data->hasStepOne(2024, 1))->toBeTrue()
            ->and($data->hasStepTwo(2024, 1))->toBeTrue()
            ->and($data->hasInput(2016, 5))->toBeFalse();
    }
});

it('returns null rather than binary when the file is still encrypted', function () {
    // What CI sees: no git-crypt key, so the blob is the raw encrypted bytes.
    file_put_contents($this->root.'/2024/01/input.txt', "\0GITCRYPT\0".random_bytes(32));

    $data = Data::unlocked($this->root);

    expect($data->input(2024, 1))->toBeNull()
        ->and($data->isLocked())->toBeTrue();
});

it('is not locked when the data is readable', function () {
    expect(Data::unlocked($this->root)->isLocked())->toBeFalse();
});

it('returns null for a day that does not exist', function () {
    $data = Data::unlocked($this->root);

    expect($data->input(2016, 5))->toBeNull()
        ->and($data->stepOne(2016, 5))->toBeNull()
        ->and($data->inputBytes(2016, 5))->toBeNull();
});

it('zero-pads the day when building paths', function () {
    // 2024/01 exists; asking for day 1 must find it.
    expect(Data::unlocked($this->root)->hasInput(2024, 1))->toBeTrue();
});
