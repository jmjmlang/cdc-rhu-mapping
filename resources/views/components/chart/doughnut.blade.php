@props([
    'id'     => 'doughnut-chart',
    'labels' => [],
    'data'   => [],
    'colors' => [],
    'height' => '320px',
    'title'  => '',
    'legend' => true,
    'cutout' => '60%',
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
    var ctx = document.getElementById('{{ $id }}');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: @json($labels),
            datasets: [{
                data: @json($data),
                backgroundColor: @json($colors),
                borderWidth: 1,
                borderColor: '#fff',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '{{ $cutout }}',
            plugins: {
                legend: {
                    display: {{ $legend ? 'true' : 'false' }},
                    position: 'right',
                    labels: { boxWidth: 12, padding: 10, font: { size: 11 } },
                },
            },
        },
    });
})();
</script>
@endpush
