<section class="panel period-dashboard">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Perkembangan Indeks Kepuasan Pengguna</h6>
            <p class="panel-subtitle">Bandingkan indeks kepuasan antarperiode sesuai filter aktif.</p>
        </div>
        <span class="chip"><i class="bi bi-funnel-fill"></i> Sesuai filter aktif</span>
    </div>

    @if($periodSatisfactionSummaries->isEmpty())
        <div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada arsip survei berperiode yang dapat dihitung.</div>
    @else
        @php($periodPreviewLimit = 6)
        @php($periodPreview = $periodSatisfactionSummaries->take($periodPreviewLimit))
        <div class="period-content-grid period-content-grid-clean">
            <div class="period-score-card trend-card">
                <span class="period-score-label">Tren Indeks Kepuasan per Periode</span>
                <div id="chart-period-trend" class="period-chart"></div>
                @if($periodSatisfactionSummaries->count() > $periodPreviewLimit)
                    <span class="period-score-note">Menampilkan {{ $periodPreviewLimit }} periode terbaru dari {{ $periodSatisfactionSummaries->count() }} periode.</span>
                @endif
            </div>
            <section class="period-table-section" aria-labelledby="period-table-title">
                <div class="period-table-section-header">
                    <div>
                        <h6 id="period-table-title">Rincian Indeks Kepuasan per Periode</h6>
                        <p>Detail perhitungan untuk periode yang ditampilkan pada grafik di atas.</p>
                    </div>
                </div>
            <div class="period-table-wrap">
                <table class="period-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            @if($canViewDashboardMetrics)
                                <th class="metric-col">Jumlah Responden<br>yang Mengisi</th>
                                <th class="metric-col">Jumlah Alumni<br>yang Dinilai</th>
                                <th>Respons</th>
                            @endif
                            <th>Indeks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodSatisfactionSummaries as $period)
                            <tr data-period-row @if($loop->index >= $periodPreviewLimit) hidden @endif>
                                <td>{{ $period['periode'] }}</td>
                                @if($canViewDashboardMetrics)
                                    <td>{{ $period['total_responden'] }}</td>
                                    <td>{{ $period['total_lulusan'] }}</td>
                                    <td>{{ number_format($period['response_rate_pct'], 1) }}%</td>
                                @endif
                                <td><strong>{{ number_format($period['skor_akhir'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($periodSatisfactionSummaries->count() > $periodPreviewLimit)
                    <div class="period-table-footer">
                        <span id="periodPaginationSummary">Menampilkan 1 hingga {{ $periodPreviewLimit }} dari {{ $periodSatisfactionSummaries->count() }} periode.</span>
                        <nav id="periodPagination" class="pagination-nav" aria-label="Halaman rincian indeks per periode"></nav>
                    </div>
                @endif
            </div>
            </section>
        </div>

        @if($periodSatisfactionSummaries->count() > $periodPreviewLimit)
            {{-- Rincian seluruh periode ditampilkan melalui pagination di tabel utama. --}}
            {{-- <div class="modal fade" id="periodSummaryModal" tabindex="-1" aria-labelledby="periodSummaryModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title" id="periodSummaryModalLabel">Seluruh Periode Indeks Kepuasan</h5>
                                <p class="mb-0 text-muted" style="font-size:.78rem;">Data mengikuti filter dashboard yang sedang aktif.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body period-modal-body">
                            <table class="period-table">
                                <thead>
                                    <tr>
                                        <th>Periode</th>
                                        @if($isAdmin)
                                            <th class="metric-col">Jumlah Responden<br>yang Mengisi</th>
                                            <th class="metric-col">Jumlah Alumni<br>yang Dinilai</th>
                                            <th>Respons</th>
                                        @endif
                                        <th>Indeks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodSatisfactionSummaries as $period)
                                        <tr>
                                            <td>{{ $period['periode'] }}</td>
                                            @if($isAdmin)
                                                <td>{{ $period['total_responden'] }}</td>
                                                <td>{{ $period['total_lulusan'] }}</td>
                                                <td>{{ number_format($period['response_rate_pct'], 1) }}%</td>
                                            @endif
                                            <td><strong>{{ number_format($period['skor_akhir'], 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}
        @endif
    @endif
</section>
