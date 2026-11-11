@props([
    'items' => [],
    'title' => 'Grafik Nilai Mata Pelajaran',
    'subtitle' => null,
])

@php
    $data = collect($items)
        ->map(function ($item) {
            $nilai = isset($item['nilai']) && is_numeric($item['nilai']) ? (int) $item['nilai'] : null;
            $kkm = isset($item['kkm']) && is_numeric($item['kkm']) ? (int) $item['kkm'] : 75;

            return [
                'label' => $item['label'] ?? '-',
                'nilai' => $nilai,
                'kkm' => $kkm,
                'tuntas' => $nilai !== null && $nilai >= $kkm,
            ];
        })
        ->values();

    $nilaiTerisi = $data->pluck('nilai')->filter(fn ($nilai) => $nilai !== null);

    $ringkasan = [
        'rata_rata' => $nilaiTerisi->isNotEmpty() ? round($nilaiTerisi->avg(), 1) : null,
        'tertinggi' => $nilaiTerisi->isNotEmpty() ? $nilaiTerisi->max() : null,
        'terendah' => $nilaiTerisi->isNotEmpty() ? $nilaiTerisi->min() : null,
        'tuntas' => $data->where('tuntas', true)->count(),
        'total' => $data->count(),
    ];

    $kartuRingkasan = [
        ['label' => 'Rata-rata', 'nilai' => $ringkasan['rata_rata'], 'icon' => 'fas fa-chart-simple', 'color' => 'var(--primary-color)'],
        ['label' => 'Tertinggi', 'nilai' => $ringkasan['tertinggi'], 'icon' => 'fas fa-arrow-trend-up', 'color' => 'var(--success-color)'],
        ['label' => 'Terendah', 'nilai' => $ringkasan['terendah'], 'icon' => 'fas fa-arrow-trend-down', 'color' => 'var(--danger-color)'],
        ['label' => 'Tuntas', 'nilai' => $ringkasan['total'] > 0 ? $ringkasan['tuntas'] . '/' . $ringkasan['total'] : null, 'icon' => 'fas fa-circle-check', 'color' => 'var(--secondary-color)'],
    ];
@endphp

<x-layout.modern-card class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="fas fa-chart-column me-2" style="color: var(--primary-color);"></i>{{ $title }}
            </h5>
            <div class="text-muted small">
                {{ $subtitle ?? 'Perbandingan nilai raport tiap mata pelajaran terhadap KKM.' }}
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3 small text-muted">
            <span><span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background: var(--success-color);"></span>Tuntas</span>
            <span><span class="d-inline-block rounded-circle me-1" style="width: 10px; height: 10px; background: var(--danger-color);"></span>Belum tuntas</span>
            <span><span class="d-inline-block me-1" style="width: 2px; height: 12px; background: var(--text-muted); vertical-align: middle;"></span>KKM</span>
        </div>
    </div>

    @if ($data->isEmpty())
        <x-ui.empty-state icon="fas fa-chart-column" title="Belum ada nilai untuk digrafikkan"
            description="Grafik akan muncul setelah guru menginput nilai raport mata pelajaran." size="sm" />
    @else
        <div class="row g-2 mb-4">
            @foreach ($kartuRingkasan as $kartu)
                <div class="col-6 col-lg-3">
                    <div class="h-100 p-3 rounded" style="background: var(--bg-tertiary);">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small fw-semibold">{{ $kartu['label'] }}</div>
                                <div class="fs-4 fw-bold" style="color: {{ $kartu['color'] }};">
                                    {{ $kartu['nilai'] ?? '-' }}
                                </div>
                            </div>
                            <i class="{{ $kartu['icon'] }}" style="color: {{ $kartu['color'] }}; opacity: 0.6;"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-column gap-3">
            @foreach ($data as $item)
                <div class="row align-items-center g-2" wire:key="nilai-chart-{{ $loop->index }}">
                    <div class="col-12 col-md-3">
                        <div class="text-truncate fw-semibold small" style="color: var(--text-primary);" title="{{ $item['label'] }}">
                            {{ $item['label'] }}
                        </div>
                    </div>
                    <div class="col-9 col-md-7">
                        <div style="position: relative; height: 22px; background: var(--bg-tertiary); border-radius: 999px;">
                            <div style="height: 100%; width: {{ max(0, min(100, $item['nilai'] ?? 0)) }}%; background: {{ $item['tuntas'] ? 'var(--success-color)' : 'var(--danger-color)' }}; border-radius: 999px; transition: width 0.4s ease;"></div>
                            <div style="position: absolute; top: -2px; bottom: -2px; left: {{ max(0, min(100, $item['kkm'])) }}%; width: 2px; background: var(--text-muted);"
                                title="KKM {{ $item['kkm'] }}"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size: 0.7rem; color: var(--text-muted);">
                            <span>0</span>
                            <span>KKM {{ $item['kkm'] }}</span>
                            <span>100</span>
                        </div>
                    </div>
                    <div class="col-3 col-md-2 text-end">
                        <span class="fw-bold" style="color: {{ $item['tuntas'] ? 'var(--success-color)' : 'var(--danger-color)' }};">
                            {{ $item['nilai'] ?? '-' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layout.modern-card>
