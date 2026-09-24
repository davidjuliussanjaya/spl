<div class="panel satisfaction-panel">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Indeks Kepuasan Pengguna</h6>
            <p class="panel-subtitle">
                @if($canViewDashboardMetrics)
                    {{ $totalResponKepuasan }} respons penilaian dari {{ $totalResponden }} responden.
                    {{ $skorKepuasan['jumlah_responden'] }} responden mengisi dari {{ $skorKepuasan['total_lulusan'] }} alumni yang dinilai ({{ number_format($skorKepuasan['response_rate_pct'], 1) }}%)
                    — {{ $skorKepuasan['rumus'] }}.
                @else
                    Ringkasan nilai kepuasan pengguna lulusan berdasarkan data survei anonim.
                @endif
            </p>
        </div>
        @if(!$kepuasanPerKategori->isEmpty())
            <div class="view-switch" role="group" aria-label="Mode tampilan kepuasan">
                <button type="button" class="active" data-view-mode="table"><i class="bi bi-table"></i></button>
                <button type="button" data-view-mode="chart"><i class="bi bi-bar-chart"></i></button>
            </div>
        @endif
    </div>

    @if($kepuasanPerKategori->isEmpty())
        <div class="empty-state"><i class="bi bi-bar-chart"></i>Belum ada data penilaian.</div>
    @else
        <div class="kepuasan-table-wrap" style="overflow-x:auto;">
            <table class="tbl-kepuasan">
                <thead>
                    <tr>
                        <th class="th-name">Jenis Kemampuan</th>
                        <th>Sangat Baik (4)</th>
                        <th>Baik (3)</th>
                        <th>Cukup (2)</th>
                        <th>Kurang (1)</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kepuasanPerKategori as $kat)
                        <tr data-kategori-row="{{ $kat['kategori'] }}">
                            <td>{{ $kat['kategori'] }}</td>
                            <td>{{ $kat['pct_sb'] }}% @if($canViewDashboardMetrics)<small class="text-muted">({{ $kat['total_respon'] }})</small>@endif</td>
                            <td>{{ $kat['pct_b'] }}% @if($canViewDashboardMetrics)<small class="text-muted">({{ $kat['total_respon'] }})</small>@endif</td>
                            <td>{{ $kat['pct_k'] }}% @if($canViewDashboardMetrics)<small class="text-muted">({{ $kat['total_respon'] }})</small>@endif</td>
                            <td>{{ $kat['pct_sk'] }}% @if($canViewDashboardMetrics)<small class="text-muted">({{ $kat['total_respon'] }})</small>@endif</td>
                            <td class="fw-bold">{{ number_format($kat['skor_akhir'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>Total</td>
                        <td>{{ $kepuasanRingkasan['total']['sb'] }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['b'] }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['k'] }}%</td>
                        <td>{{ $kepuasanRingkasan['total']['sk'] }}%</td>
                        <td>{{ $skorKepuasan['rumus'] }}</td>
                    </tr>
                    <tr>
                        <td>Rata-Rata</td>
                        <td>{{ $kepuasanRingkasan['rata']['sb'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['b'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['k'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['sk'] }}%</td>
                        <td>{{ number_format($skorKepuasan['skor_akhir'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="kepuasan-chart-wrap"><div id="chart-kepuasan-stack"></div></div>
    @endif
</div>
