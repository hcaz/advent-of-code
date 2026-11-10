<?php

declare(strict_types=1);

use Aoc\ProblemFile;

/**
 * ProblemFile mutates files whose bodies are hand-written and exist nowhere
 * else, so the property that actually matters is: writing must never alter the
 * body. Every test here asserts that, not just the happy path.
 */
beforeEach(function () {
    $this->dir = sys_get_temp_dir().'/aoc-problemfile-'.bin2hex(random_bytes(6));
    mkdir($this->dir);
});

afterEach(function () {
    foreach (glob($this->dir.'/*') ?: [] as $f) {
        unlink($f);
    }
    @rmdir($this->dir);
});

function writeFixture(string $dir, string $contents): string
{
    $path = $dir.'/2024-01.md';
    file_put_contents($path, $contents);

    return $path;
}

it('round-trips a fully populated file without touching the body', function () {
    $body = "Some hand-written notes.\n\nWith a second paragraph.\n";
    $path = writeFixture($this->dir, <<<MD
        ---
        year: 2024
        day: 1
        title: 'Historian Hysteria'
        answer_one: '2166959'
        solved_at: '2024-12-02'
        ai_part_one: false
        ---
        {$body}
        MD);

    $file = ProblemFile::read($path);

    expect($file->get('year'))->toBe(2024)
        ->and($file->get('title'))->toBe('Historian Hysteria')
        ->and($file->get('answer_one'))->toBe('2166959')
        ->and($file->get('ai_part_one'))->toBeFalse()
        ->and($file->body())->toBe($body);

    $file->write();

    expect(ProblemFile::read($path)->body())->toBe($body);
});

it('preserves a body containing a --- delimiter line', function () {
    $body = "Intro paragraph.\n\n---\n\nAfter a horizontal rule.\n";
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\n---\n".$body);

    $file = ProblemFile::read($path);
    expect($file->body())->toBe($body);

    $file->set('answer_one', '42')->write();

    $reread = ProblemFile::read($path);
    expect($reread->body())->toBe($body)
        ->and($reread->get('answer_one'))->toBe('42')
        ->and($reread->get('year'))->toBe(2024);
});

it('preserves an empty body', function () {
    $path = writeFixture($this->dir, "---\nyear: 2016\nday: 5\n---\n");

    $file = ProblemFile::read($path);
    expect($file->body())->toBe('');

    $file->set('gitea_issue', 57)->write();

    expect(ProblemFile::read($path)->body())->toBe('')
        ->and(ProblemFile::read($path)->get('gitea_issue'))->toBe(57);
});

it('leaves keys it was not asked to change untouched', function () {
    $path = writeFixture($this->dir, <<<'MD'
        ---
        year: 2024
        day: 1
        title: 'Historian Hysteria'
        solved_at: '2024-12-02'
        ai_part_two: true
        ---
        Notes.
        MD);

    ProblemFile::read($path)->set('answer_one', '123')->write();

    $file = ProblemFile::read($path);

    expect($file->get('title'))->toBe('Historian Hysteria')
        ->and($file->get('solved_at'))->toBe('2024-12-02')
        ->and($file->get('ai_part_two'))->toBeTrue()
        ->and($file->get('answer_one'))->toBe('123');
});

it('keeps unrecognised hand-added keys', function () {
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\nmy_own_note: keep me\n---\nBody.\n");

    ProblemFile::read($path)->set('answer_one', '1')->write();

    expect(ProblemFile::read($path)->get('my_own_note'))->toBe('keep me');
});

it('is idempotent: a second write produces identical bytes', function () {
    $path = writeFixture($this->dir, <<<'MD'
        ---
        year: 2024
        day: 1
        title: 'Historian Hysteria'
        answer_one: '2166959'
        notes_one: { pairs: 1000 }
        ---
        Notes here.
        MD);

    ProblemFile::read($path)->write();
    $first = file_get_contents($path);

    ProblemFile::read($path)->write();

    expect(file_get_contents($path))->toBe($first);
});

it('removes a key when set to null rather than leaving it empty', function () {
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\nanswer_one: '5'\n---\nBody.\n");

    ProblemFile::read($path)->set('answer_one', null)->write();

    expect(ProblemFile::read($path)->has('answer_one'))->toBeFalse();
});

it('round-trips notes maps of scalars', function () {
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\n---\nBody.\n");

    ProblemFile::read($path)
        ->set('notes_one', ['pairs' => 1000, 'max' => '99999', 'exact' => true])
        ->write();

    expect(ProblemFile::read($path)->get('notes_one'))
        ->toBe(['pairs' => 1000, 'max' => '99999', 'exact' => true]);
});

it('treats a file with no front matter as all body', function () {
    $path = writeFixture($this->dir, "Just prose, no front matter.\n");

    expect(ProblemFile::read($path)->body())->toBe("Just prose, no front matter.\n")
        ->and(ProblemFile::read($path)->frontMatter())->toBe([]);
});

it('rejects unterminated front matter instead of guessing', function () {
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\n");

    expect(fn () => ProblemFile::read($path))
        ->toThrow(RuntimeException::class, 'Unterminated front matter');
});

it('does not leave temp files behind', function () {
    $path = writeFixture($this->dir, "---\nyear: 2024\nday: 1\n---\nBody.\n");

    ProblemFile::read($path)->set('answer_one', '7')->write();

    expect(glob($this->dir.'/.problem-*') ?: [])->toBeEmpty();
});
