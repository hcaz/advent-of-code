@extends('_layouts.main')

@php
    $stars = aoc_stars($page);
    $puzzle = aoc_puzzle((int) $page->year, (int) $page->day, (bool) $page->show_puzzle_text);
    $retroactive = aoc_is_retroactive($page->solved_at ?? null, (int) $page->year);
@endphp

@section('title', aoc_title($page).' · '.$page->year)

@section('body')
    <article class="problem">
        <nav class="breadcrumb">
            <a href="/">All years</a> /
            <a href="/{{ $page->year }}">{{ $page->year }}</a>
        </nav>

        <header>
            <p class="eyebrow">{{ $page->year }} &middot; Day {{ $page->day }}</p>
            <h1>{{ aoc_title($page) }}</h1>

            <p class="meta">
                <span class="stars" title="{{ $stars }} of 2 stars">
                    {{ str_repeat('★', $stars) }}{{ str_repeat('☆', 2 - $stars) }}
                </span>

                @if ($page->solved_at ?? null)
                    <span>Solved {{ $page->solved_at }}</span>
                    @if ($retroactive)
                        <span class="tag" title="Solved outside this year's event">retroactive</span>
                    @endif
                @elseif ($puzzle['has_input'])
                    <span class="tag">in progress</span>
                @endif

                <a href="{{ aoc_link((int) $page->year, (int) $page->day) }}">Puzzle on adventofcode.com</a>
            </p>
        </header>

        <section class="answers">
            <h2>Answers</h2>
            <dl>
                @foreach (['one' => 'Part one', 'two' => 'Part two'] as $part => $label)
                    @php $answer = $page->{'answer_'.$part} ?? null; @endphp
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd>
                            @if ($answer)
                                <code>{{ $answer }}</code>
                            @else
                                <span class="unsolved">not solved yet</span>
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>

            @if (($page->ai_part_one ?? false) || ($page->ai_part_two ?? false))
                <p class="ai-note">
                    AI assistance used on
                    {{ collect(['one' => $page->ai_part_one ?? false, 'two' => $page->ai_part_two ?? false])
                        ->filter()->keys()->map(fn ($p) => 'part '.$p)->implode(' and ') }}.
                </p>
            @endif
        </section>

        @foreach (['one' => 'Part one', 'two' => 'Part two'] as $part => $label)
            @php $notes = $page->{'notes_'.$part} ?? null; @endphp
            @if (is_array($notes) && $notes !== [])
                <section class="notes">
                    <h2>{{ $label }} &mdash; working out</h2>
                    <dl>
                        @foreach ($notes as $noteLabel => $value)
                            <div>
                                <dt>{{ $noteLabel }}</dt>
                                <dd><code>{{ $value }}</code></dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endif
        @endforeach

        @if ($page->getContent())
            <section class="prose">
                {!! $page->getContent() !!}
            </section>
        @endif

        {{--
            Puzzle text and input are rendered ONLY where the gate is open,
            which is local builds. aoc_puzzle() returns null for all of this in
            production, so there is nothing here to leak even if this block is
            edited carelessly.
        --}}
        @if ($page->show_puzzle_text)
            <section class="local-only">
                <h2>Local only</h2>
                <p class="warning">
                    This section is never rendered in production. Puzzle text and inputs
                    are not republished.
                </p>

                @if ($puzzle['locked'])
                    <p>data/ is locked &mdash; no git-crypt key available.</p>
                @else
                    @if ($puzzle['input'] !== null)
                        <details>
                            <summary>Input ({{ number_format((int) $puzzle['input_bytes']) }} bytes)</summary>
                            <pre>{{ implode("\n", array_slice(explode("\n", $puzzle['input']), 0, 10)) }}</pre>
                            <p>First 10 lines.</p>
                        </details>
                    @endif

                    @foreach (['step_one' => 'Part one text', 'step_two' => 'Part two text'] as $key => $label)
                        @if ($puzzle[$key] !== null)
                            <details>
                                <summary>{{ $label }}</summary>
                                <pre>{{ $puzzle[$key] }}</pre>
                            </details>
                        @endif
                    @endforeach
                @endif
            </section>
        @endif
    </article>
@endsection
