# Advent of Code

My solutions to [Advent of Code](https://adventofcode.com/), every year since 2015, written in PHP
and published as a static site built with [Jigsaw](https://jigsaw.tighten.com/).

Puzzle inputs and puzzle text are **not** in this repository. They live under `data/` encrypted with
`git-crypt`, and the site build cannot read them unless it is explicitly told to, which only happens
for local builds. This is deliberate — see [the AoC about page](https://adventofcode.com/about):

> If you're posting a code repository somewhere, please don't include parts of Advent of Code like
> the puzzle text or your inputs.

Here is a list of the challenges I have completed:

**★ 40 collected.**

### 2024 — ★ 14

- [Historian Hysteria](https://adventofcode.com/2024/day/1) ★★ - `bin/aoc run 2024 1`
- [Red-Nosed Reports](https://adventofcode.com/2024/day/2) ★★ - `bin/aoc run 2024 2`
- [Mull It Over](https://adventofcode.com/2024/day/3) ★★ - `bin/aoc run 2024 3`
- [Ceres Search](https://adventofcode.com/2024/day/4) ★★ - `bin/aoc run 2024 4`
- [Print Queue](https://adventofcode.com/2024/day/5) ★★ - `bin/aoc run 2024 5`
- [Guard Gallivant](https://adventofcode.com/2024/day/6) ★★ - `bin/aoc run 2024 6`
- [Hoof It](https://adventofcode.com/2024/day/10) ★★ - `bin/aoc run 2024 10`

### 2023 — ★ 1

- [Trebuchet?!](https://adventofcode.com/2023/day/1) ★ - `bin/aoc run 2023 1` _(retroactive)_

### 2022 — ★ 13

- [Calorie Counting](https://adventofcode.com/2022/day/1) ★★ - `bin/aoc run 2022 1`
- [Rock Paper Scissors](https://adventofcode.com/2022/day/2) ★★ - `bin/aoc run 2022 2`
- [Rucksack Reorganization](https://adventofcode.com/2022/day/3) ★★ - `bin/aoc run 2022 3`
- [Camp Cleanup](https://adventofcode.com/2022/day/4) ★★ - `bin/aoc run 2022 4`
- [Supply Stacks](https://adventofcode.com/2022/day/5) ★★ - `bin/aoc run 2022 5`
- [Tuning Trouble](https://adventofcode.com/2022/day/6) ★★ - `bin/aoc run 2022 6`
- [No Space Left On Device](https://adventofcode.com/2022/day/7) ★ - `bin/aoc run 2022 7`

### 2015 — ★ 12

- [Not Quite Lisp](https://adventofcode.com/2015/day/1) ★★ - `bin/aoc run 2015 1` _(retroactive)_
- [I Was Told There Would Be No Math](https://adventofcode.com/2015/day/2) ★★ - `bin/aoc run 2015 2` _(retroactive)_
- [Perfectly Spherical Houses in a Vacuum](https://adventofcode.com/2015/day/3) ★★ - `bin/aoc run 2015 3` _(retroactive)_
- [The Ideal Stocking Stuffer](https://adventofcode.com/2015/day/4) ★★ - `bin/aoc run 2015 4` _(retroactive)_
- [Doesn't He Have Intern-Elves For This?](https://adventofcode.com/2015/day/5) ★★ - `bin/aoc run 2015 5` _(retroactive)_
- [Probably a Fire Hazard](https://adventofcode.com/2015/day/6) ★★ - `bin/aoc run 2015 6` _(retroactive)_

## Getting started

### Prerequisites

PHP 8.4 or newer and Composer. There is no Node toolchain — the stylesheet is hand-written and
served as-is.

### Installing

```
composer install
```

### Running a solution

Solutions are plain classes under `src/Solutions/`, run through a small CLI:

```
bin/aoc run                      # every ported day
bin/aoc run 2024                 # one year
bin/aoc run 2024 1               # one day
bin/aoc run 2024 1 --part=two    # one part
bin/aoc run --write              # record answers and notes into the problem files
```

This needs the puzzle inputs, so it only works with the `git-crypt` key:

```
git-crypt unlock
```

### Building the site

```
vendor/bin/jigsaw build local        # includes puzzle text, never committed or deployed
vendor/bin/jigsaw build production   # answers and notes only
```

A local build renders the puzzle text and a preview of the input so the pages are useful while
working on a day. A production build cannot, because `src/Data.php` refuses to read it unless
`show_puzzle_text` is on, and that is only set in `config.local.php`.

### Local configuration

Copy `config.local.php.example` to `config.local.php` and fill it in. That file is gitignored and
holds the two things that must never be committed: the flag that lets the build read puzzle text,
and the Gitea credentials used by the issue sync.

### Checks

```
composer lint          # Pint
composer stan          # PHPStan
composer test          # Pest, structural checks only
composer check-leaks   # scan a production build for puzzle content
vendor/bin/pest --group=solutions    # verify every recorded answer (needs the key, slow)
```

### Keeping things in sync

```
composer sync-readme                        # regenerate this README
php scripts/sync-readme.php --check         # fail if it is out of date
composer sync-issues                        # dry-run the Gitea issue sync
php scripts/sync-gitea-issues.php           # apply it
```

The issue sync also creates problem files and year pages for newly released days, so running it in
December is enough to pick up a new puzzle. It embeds puzzle text in issue bodies, so it needs the
`git-crypt` key and never runs in CI.

## How it fits together

| Path | What it is |
|---|---|
| `source/_problems/<year>-<day>.md` | One file per puzzle day — **the single source of truth** for titles, answers, notes, AI usage and the date solved |
| `src/Solutions/Year<year>/Day<day>.php` | The solution, returning `Result{answer, notes}` |
| `data/<year>/<day>/` | Puzzle input and text, `git-crypt` encrypted, never published |
| `scripts/` | README sync, Gitea issue sync, and the leak check |
| `config.local.php` | Local-only, gitignored: puzzle text flag and Gitea credentials |

Both `bin/aoc` and the Gitea sync write back into the problem files through `src/ProblemFile.php`,
which only ever touches its own front matter keys and leaves hand-written prose byte-for-byte alone.

## Authors

- **Zachary Claret-Scott** -
  [hcaz](https://hcaz.uk)

## Code hosting

This repository is stored on a private git server, however it is mirrored to the following public git servers:
- https://github.com/hcaz/advent-of-code
- https://gitlab.com/zachary.claretscott/advent-of-code
