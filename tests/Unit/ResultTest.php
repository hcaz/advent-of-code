<?php

declare(strict_types=1);

use Aoc\Result;

it('always exposes the answer as a string', function () {
    expect(Result::of(280)->answer)->toBe('280')
        ->and(Result::of('DHBJQJCCW')->answer)->toBe('DHBJQJCCW')
        ->and((new Result('1797'))->answer)->toBe('1797');
});

it('accepts scalar notes', function () {
    $result = Result::of(1, ['rounds' => 2500, 'ratio' => 0.5, 'exact' => true, 'grid' => '130x130']);

    expect($result->notes)->toBe([
        'rounds' => 2500,
        'ratio' => 0.5,
        'exact' => true,
        'grid' => '130x130',
    ]);
});

it('rejects a note holding a structure, which is how input would leak', function () {
    // The old TUI printed whole parsed grids; this is the shape that stops a
    // verbatim port republishing input-derived content.
    expect(fn () => Result::of(1, ['stacks' => [['A', 'B'], ['C']]]))
        ->toThrow(InvalidArgumentException::class, 'must be a scalar');
});

it('rejects a multi-line note', function () {
    expect(fn () => Result::of(1, ['grid' => "..#..\n.#...\n"]))
        ->toThrow(InvalidArgumentException::class, 'multi-line');
});

it('rejects a long note that could be an input excerpt', function () {
    expect(fn () => Result::of(1, ['dump' => str_repeat('64256   78813 ', 20)]))
        ->toThrow(InvalidArgumentException::class, 'over 120 characters');
});

it('rejects a null note rather than storing an empty value', function () {
    expect(fn () => Result::of(1, ['thing' => null]))
        ->toThrow(InvalidArgumentException::class, 'must be a scalar');
});

it('rejects non-string note labels', function () {
    expect(fn () => Result::of(1, ['first', 'second']))
        ->toThrow(InvalidArgumentException::class, 'Note labels must be non-empty strings');
});

it('allows no notes at all', function () {
    expect(Result::of(42)->notes)->toBe([]);
});
