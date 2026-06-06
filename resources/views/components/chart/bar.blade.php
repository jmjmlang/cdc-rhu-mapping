@props([
    'id'       => 'bar-chart',
    'labels'   => [],
    'datasets' => [],
    'height'   => '320px',
    'stacked'  => false,
    'horizontal' => false,
    'title'    => '',
    'legend'   => true,
])

<div>
    @if($title)
        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ $title }}</h3>
    @endif
    <div style="position:relative; height:{{ $height }};">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const ctx = document.getElementById('{{ $id }}');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: @json($datasets),
        },
        options: {
            indexAxis: '{{ $horizontal ? "y" : "x" }}',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: {{ $legend ? 'true' : 'false' }} },
            },
            scales: {
                x: { stacked: {{ $stacked ? 'true' : 'false' }}, grid: { display: false } },
                y: { stacked: {{ $stacked ? 'true' : 'false' }}, beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
})();
</script>
@endpush
