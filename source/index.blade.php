@extends('_layouts.main')

@php
    $years = $years->sortByDesc('year');
    $totalStars = $problems->sum(fn ($p) => aoc_stars($p));
@endphp

@section('body')
    <header class="site-header">
        <h1>Advent of Code</h1>
        <p>{{ $page->description }}</p>
        <p class="meta"><span class="stars">★ {{ $totalStars }}</span> collected so far</p>
    </header>

    <ul class="year-list">
        @foreach ($years as $year)
            @php
                $y = (int) $year->year;
                $cells = aoc_problems_for_year($problems, $y);
                $started = collect($cells)->filter()->filter(fn ($p) => aoc_is_attempted($p));
                $stars = collect($cells)->filter()->sum(fn ($p) => aoc_stars($p));
            @endphp
            <li>
                <a href="{{ $year->getUrl() }}">
                    <span class="year-list__year">{{ $y }}</span>
                    <span class="year-list__stars">★ {{ $stars }} / {{ 2 * count($cells) }}</span>
                    <span class="year-list__count">{{ $started->count() }} / {{ count($cells) }} days started</span>
                </a>
            </li>
        @endforeach
    </ul>
@endsection
