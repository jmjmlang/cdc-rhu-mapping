{{--
  Reusable date-time display.

  Props:
    date        Carbon instance or date string (the date part)
    time        Carbon instance or date string (time part; defaults to $date if omitted)
    dateFormat  strftime format for the date portion (default: 'M d, Y')
    timeFormat  format for time (default: 'g:i A')
    showTime    whether to show the time part (default: false)

  Usage (date only):
    <x-ui.datetime :date="$report->report_date" />

  Usage (date + time, evenly weighted):
    <x-ui.datetime :date="$report->report_date" :time="$report->created_at" show-time />
--}}

@props([
    'date',
    'time'       => null,
    'dateFormat' => 'M d, Y',
    'timeFormat' => 'g:i A',
    'showTime'   => false,
])

@php
    $dt     = $date instanceof \Illuminate\Support\Carbon\Carbon || $date instanceof \Carbon\Carbon
                  ? $date
                  : \Illuminate\Support\Carbon::parse($date);
    $timeDt = null;
    if ($showTime) {
        $src    = $time ?? $date;
        $timeDt = $src instanceof \Illuminate\Support\Carbon\Carbon || $src instanceof \Carbon\Carbon
                      ? $src
                      : \Illuminate\Support\Carbon::parse($src);
    }
@endphp

<span {{ $attributes->merge(['class' => 'whitespace-nowrap']) }}>
    <span class="text-gray-700">{{ $dt->format($dateFormat) }}</span>
    @if($timeDt)
        <span class="text-gray-400 mx-1">&middot;</span>
        <span class="text-gray-500 font-normal">{{ $timeDt->format($timeFormat) }}</span>
    @endif
</span>
