<section class="panel category-period-comparison">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Perbandingan Nilai Kategori Antarperiode</h6>
            <p class="panel-subtitle">Heatmap memudahkan melihat kategori yang meningkat atau menurun pada periode/tahun sebelumnya.</p>
        </div>
        <span class="chip"><i class="bi bi-grid-3x3-gap-fill"></i> Heatmap 0-4</span>
    </div>

    @if($categoryPeriodComparison->isEmpty())
        <div class="empty-state"><i class="bi bi-bar-chart-line"></i>Belum ada nilai kategori yang dapat dibandingkan.</div>
    @elseif(count($categoryComparisonPeriods) < 2)
        <div class="empty-state"><i class="bi bi-calendar-plus"></i>Pilih atau tunggu minimal dua periode survei untuk melihat perbandingan nilai kategori.</div>
    @else
        <div class="category-comparison-chart-wrap">
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
