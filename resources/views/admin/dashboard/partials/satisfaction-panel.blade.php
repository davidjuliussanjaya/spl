<div class="panel satisfaction-panel">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Tingkat Kepuasan Pengguna</h6>
            <p class="panel-subtitle">
                {{ $totalResponKepuasan }} respons penilaian dari {{ $totalResponden }} responden.
                NL {{ $skorKepuasan['jumlah_responden'] }} / NJ {{ $skorKepuasan['total_lulusan'] }} = {{ number_format($skorKepuasan['response_rate_pct'], 1) }}%
                — {{ $skorKepuasan['rumus'] }}.
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
                        <th>Skor Murni</th>
                        <th>Skor Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kepuasanPerKategori as $kat)
                        <tr data-kategori-row="{{ $kat['kategori'] }}">
                            <td>{{ $kat['kategori'] }}</td>
                            <td>{{ $kat['pct_sb'] }}% <small class="text-muted">({{ $kat['total_respon'] }})</small></td>
                            <td>{{ $kat['pct_b'] }}% <small class="text-muted">({{ $kat['total_respon'] }})</small></td>
                            <td>{{ $kat['pct_k'] }}% <small class="text-muted">({{ $kat['total_respon'] }})</small></td>
                            <td>{{ $kat['pct_sk'] }}% <small class="text-muted">({{ $kat['total_respon'] }})</small></td>
                            <td>{{ number_format($kat['skor_murni'], 2) }}</td>
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
                        <td colspan="2">{{ $skorKepuasan['rumus'] }}</td>
                    </tr>
                    <tr>
                        <td>Rata-Rata</td>
                        <td>{{ $kepuasanRingkasan['rata']['sb'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['b'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['k'] }}%</td>
                        <td>{{ $kepuasanRingkasan['rata']['sk'] }}%</td>
                        <td>{{ number_format($skorKepuasan['skor_murni'], 2) }}</td>
                        <td>{{ number_format($skorKepuasan['skor_akhir'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="kepuasan-chart-wrap"><div id="chart-kepuasan-stack"></div></div>
    @endif
</div>
