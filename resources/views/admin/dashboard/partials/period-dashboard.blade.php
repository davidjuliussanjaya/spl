<section class="panel period-dashboard">
    <div class="panel-header">
        <div>
            <h6 class="panel-title">Dashboard Periode &amp; Indeks Global</h6>
            <p class="panel-subtitle">Setiap periode dihitung sendiri agar perbedaan kategori, jumlah soal, dan label jawaban tidak tercampur.</p>
        </div>
        <span class="chip"><i class="bi bi-check2-circle"></i> Rekomendasi: berbobot per periode</span>
    </div>

    @if($periodSatisfactionSummaries->isEmpty())
        <div class="empty-state"><i class="bi bi-calendar-x"></i>Belum ada arsip survei berperiode yang dapat dihitung.</div>
    @else
        <div class="period-score-grid">
            <div class="period-score-card recommended">
                <span class="period-score-label">Indeks Global Tertimbang</span>
                <strong class="period-score-value">{{ number_format($globalPeriodScore['skor_akhir'], 2) }} <small>/ 4.00</small></strong>
                <span class="period-score-note">Rata-rata skor akhir tiap periode, dibobotkan dengan jumlah lulusan (NJ).</span>
            </div>
            <div class="period-score-card">
                <span class="period-score-label">Cakupan Global Periode</span>
                <strong class="period-score-value">{{ $globalPeriodScore['total_responden'] }} <small>NL</small></strong>
                <span class="period-score-note">Dari {{ $globalPeriodScore['total_lulusan'] }} NJ · respons {{ number_format($globalPeriodScore['response_rate_pct'], 1) }}% (NL dijumlahkan per periode).</span>
            </div>
            <div class="period-score-card">
                <span class="period-score-label">Pembanding: Gabungan Respons</span>
                <strong class="period-score-value">{{ number_format($skorKepuasan['skor_akhir'], 2) }} <small>/ 4.00</small></strong>
                <span class="period-score-note">Metode lama: seluruh respons dalam filter dihitung sebagai satu kelompok.</span>
            </div>
        </div>

        <div class="period-content-grid">
            <div class="period-score-card">
                <span class="period-score-label">Tren Skor Akhir per Periode</span>
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
                            <th>Skor Akhir</th>
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

        <div class="period-category-list">
            @foreach($periodSatisfactionSummaries as $index => $period)
                <details class="period-detail" {{ $index === 0 ? 'open' : '' }}>
                    <summary>
                        <div>
                            <div class="period-detail-title">Kategori instrumen periode {{ $period['periode'] }}</div>
                            <div class="period-detail-meta">{{ count($period['kategori']) }} kategori · {{ $period['total_rating'] }} respons penilaian · skor akhir {{ number_format($period['skor_akhir'], 2) }}</div>
                        </div>
                    </summary>
                    @if(empty($period['kategori']))
                        <div class="empty-state">Belum ada jawaban rating pada periode ini.</div>
                    @else
                        <div style="overflow-x:auto;">
                            <table class="period-category-table">
                                <thead>
                                    <tr>
                                        <th>Kategori</th>
                                        <th>Respons</th>
                                        <th>Skor Murni</th>
                                        <th>Skor Akhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($period['kategori'] as $kategori)
                                        <tr>
                                            <td>{{ $kategori['kategori'] }}</td>
                                            <td>{{ $kategori['total_respon'] }}</td>
                                            <td>{{ number_format($kategori['skor_murni'], 2) }}</td>
                                            <td><strong>{{ number_format($kategori['skor_akhir'], 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </details>
            @endforeach
        </div>
    @endif
</section>
