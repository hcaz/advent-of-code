@extends('_layouts.main')

@php
    $year = (int) $page->year;
    $cells = aoc_problems_for_year($problems, $year);
    $earned = collect($cells)->filter()->sum(fn ($p) => aoc_stars($p));
    $attempted = collect($cells)->filter()->filter(fn ($p) => aoc_is_attempted($p))->count();
@endphp

@section('title', 'Advent of Code '.$year)

@section('body')
    <div class="year">
        <nav class="breadcrumb"><a href="/">All years</a></nav>

        <header>
            <h1>{{ $year }}</h1>
            <p class="meta">
                <span class="stars">★ {{ $earned }} / {{ 2 * count($cells) }}</span>
                <span>{{ $attempted }} of {{ count($cells) }} days started</span>
            </p>
        </header>

        <ol class="calendar">
            @foreach ($cells as $day => $problem)
                @php
                    $stars = $problem ? aoc_stars($problem) : 0;
                    $started = $problem && aoc_is_attempted($problem);
                @endphp
                <li class="day day--stars-{{ $stars }}{{ $started ? '' : ' day--empty' }}">
                    @if ($started)
                        <a href="{{ $problem->getUrl() }}">
                            <span class="day__number">{{ $day }}</span>
                            <span class="day__title">{{ aoc_title($problem) }}</span>
                            <span class="day__stars">{{ str_repeat('★', $stars) }}{{ str_repeat('☆', 2 - $stars) }}</span>
                        </a>
                    @else
                        <a href="{{ aoc_link($year, $day) }}" class="day__external">
                            <span class="day__number">{{ $day }}</span>
                            <span class="day__title">not attempted</span>
                            <span class="day__stars">☆☆</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>
@endsection
