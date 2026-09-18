<section class="panel period-dashboard">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Perkembangan Indeks Kepuasan Pengguna</h6>
            <p class="panel-subtitle">Bandingkan indeks kepuasan dan cakupan respons antarperiode sesuai filter aktif.</p>
        </div>
        <span class="chip"><i class="bi bi-funnel-fill"></i> Sesuai filter aktif</span>
    </div>

    @if($periodSatisfactionSummaries->isEmpty())
        <div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada arsip survei berperiode yang dapat dihitung.</div>
    @else
        <div class="period-content-grid period-content-grid-clean">
            <div class="period-score-card trend-card">
                <span class="period-score-label">Tren Indeks Kepuasan per Periode</span>
                <div id="chart-period-trend" class="period-chart"></div>
            </div>
            <div class="period-table-wrap">
                <table class="period-table">
                    <thead>
                        <tr>
                            <th>Periode</th>
                            <th>NL</th>
                            <th>NJ</th>
                            <th>Respons</th>
                            <th>Skor Murni</th>
                            <th>Faktor</th>
                            <th>Indeks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodSatisfactionSummaries as $period)
                            <tr>
                                <td>{{ $period['periode'] }}</td>
                                <td>{{ $period['total_responden'] }}</td>
                                <td>{{ $period['total_lulusan'] }}</td>
                                <td>{{ number_format($period['response_rate_pct'], 1) }}%</td>
                                <td>{{ number_format($period['skor_murni'], 2) }}</td>
                                <td>{{ number_format($period['faktor_pembobot'], 2) }}</td>
                                <td><strong>{{ number_format($period['skor_akhir'], 2) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>
