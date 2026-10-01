<section class="panel category-period-comparison">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Perbandingan Nilai Kategori Antarperiode</h6>
            <p class="panel-subtitle">Heatmap memudahkan melihat kategori yang meningkat atau menurun pada periode/tahun sebelumnya.</p>
        </div>
        <div class="category-comparison-actions">
            <span class="chip"><i class="bi bi-grid-3x3-gap-fill"></i> Heatmap 0-4</span>
        </div>
    </div>

    @if($categoryPeriodComparison->isEmpty())
        <div class="empty-state"><i class="bi bi-bar-chart-line"></i>Belum ada nilai kategori yang dapat dibandingkan.</div>
    @elseif(count($categoryComparisonPeriods) < 2)
        <div class="empty-state"><i class="bi bi-calendar-plus"></i>Pilih atau tunggu minimal dua periode survei untuk melihat perbandingan nilai kategori.</div>
    @else
        <div class="category-comparison-chart-wrap">
            <div class="heatmap-rating-legend" role="group" aria-label="Filter tingkat nilai heatmap">
                <button type="button" class="heatmap-legend-item is-active" data-rating-filter="all" aria-pressed="true">Semua nilai</button>
                <button type="button" class="heatmap-legend-item heatmap-legend-kurang" data-rating-filter="kurang" aria-pressed="false"><span></span>Kurang</button>
                <button type="button" class="heatmap-legend-item heatmap-legend-cukup" data-rating-filter="cukup" aria-pressed="false"><span></span>Cukup</button>
                <button type="button" class="heatmap-legend-item heatmap-legend-baik" data-rating-filter="baik" aria-pressed="false"><span></span>Baik</button>
                <button type="button" class="heatmap-legend-item heatmap-legend-sangat-baik" data-rating-filter="sangat-baik" aria-pressed="false"><span></span>Sangat Baik</button>
            </div>
            <div id="chart-category-period-comparison"></div>
        </div>
        {{-- Heatmap menjadi satu-satunya tampilan perbandingan kategori. --}}
        {{-- <div class="category-comparison-table-wrap">
            <table class="category-comparison-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        @foreach($categoryComparisonPeriods as $period)
                            <th>{{ $period['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($categoryPeriodComparison as $comparison)
                        <tr>
                            <td>{{ $comparison['kategori'] }}</td>
                            @foreach($comparison['scores'] as $score)
                                <td>{{ $score === null ? '—' : number_format($score, 2) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
    @endif
</section>
