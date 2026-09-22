@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    :root {
        --brand-50: #eff6ff;
        --brand-100: #bfdbfe;
        --brand-500: #2563eb;
        --brand-700: #1d4ed8;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-700: #334155;
        --slate-900: #0f172a;
        --radius: 12px;
        --shadow: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
        --shadow-md: 0 4px 16px rgba(0,0,0,.07);
    }

    .db-wrap { display: flex; flex-direction: column; gap: 1rem; }
    .db-header { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; }
    .db-header h3 { font-size: 1.25rem; font-weight: 700; color: var(--slate-900); margin: 0; }
    .db-header p { font-size: .82rem; color: var(--slate-400); margin: .2rem 0 0; }
    .dashboard-header-actions { display: flex; align-items: end; justify-content: flex-end; gap: .55rem; flex-wrap: wrap; }
    .dashboard-filter-form { display: flex; align-items: end; gap: .55rem; flex-wrap: wrap; }
    .dashboard-filter-field { min-width: 170px; }
    .dashboard-filter-field.prodi { min-width: 210px; }
    .dashboard-filter-field .form-label { font-size: .66rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .25rem; }
    .dashboard-filter-form .form-select { font-size: .8rem; min-height: 34px; border-color: var(--slate-200); border-radius: 8px; }
    .dashboard-filter-form .form-select:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
    .dashboard-filter-actions { display: flex; gap: .4rem; }
    @media(max-width:767px) { .dashboard-filter-form { width: 100%; } .dashboard-filter-field, .dashboard-filter-field.prodi { flex: 1 1 calc(50% - .3rem); min-width: 0; } }

    .periode-dropdown { width: 100%; }
    .periode-toggle {
        width: 100%; min-height: 31px; background: #fff; border: 1px solid var(--slate-200);
        display: flex; align-items: center; justify-content: space-between; gap: .5rem;
        padding: .25rem .5rem; text-align: left;
    }
    .periode-toggle span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .periode-toggle:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
    .periode-menu {
        width: 100%; max-height: 220px; overflow-y: auto; padding: .45rem;
        border: 1px solid var(--slate-200); border-radius: 8px; box-shadow: var(--shadow-md);
    }
    .periode-option {
        display: flex; align-items: center; gap: .45rem; padding: .32rem .4rem;
        border-radius: 6px; font-size: .82rem; color: var(--slate-700); cursor: pointer;
    }
    .periode-option:hover { background: var(--slate-50); }
    .periode-option input { accent-color: var(--brand-500); }
    .btn-apply {
        font-size: .8rem; font-weight: 600; padding: .42rem 1rem;
        background: var(--brand-500); color: #fff; border: none; border-radius: 8px;
    }
    .btn-apply:hover { background: var(--brand-700); color: #fff; }
    .btn-reset { font-size: .8rem; padding: .42rem .85rem; border-radius: 8px; }
    .btn-extend {
        border: 1px solid var(--slate-200); background: #fff; color: var(--slate-700);
        border-radius: 8px; font-size: .75rem; font-weight: 600; padding: .32rem .65rem;
        display: inline-flex; align-items: center; gap: .3rem;
    }
    .btn-extend:hover { border-color: var(--brand-100); color: var(--brand-700); background: var(--brand-50); }
    .panel-actions { display: inline-flex; align-items: center; gap: .4rem; flex-wrap: wrap; }
    .btn-back {
        display: none; border: 1px solid var(--brand-100); background: var(--brand-50); color: var(--brand-700);
        border-radius: 8px; font-size: .75rem; font-weight: 700; padding: .32rem .65rem;
        align-items: center; gap: .3rem;
    }
    .btn-back.show { display: inline-flex; }
    .chip {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .72rem; font-weight: 500; padding: .2rem .6rem;
        border-radius: 50px; background: var(--brand-50);
        color: var(--brand-700); border: 1px solid var(--brand-100);
    }

    .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: .85rem; }
    @media(max-width:1399px) { .stat-grid { grid-template-columns: repeat(3, 1fr); } }
    @media(max-width:991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:575px) { .stat-grid { grid-template-columns: 1fr; gap: .7rem; } }

    .panel, .stat-card {
        background: #fff; border: 1px solid var(--slate-200);
        border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;
    }
    .panel:hover, .stat-card:hover { box-shadow: var(--shadow-md); }
    .stat-card {
        padding: 1rem 1.1rem; position: relative; transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--brand-500);
    }
    .stat-card.green::before { background: #0f766e; }
    .stat-card.amber::before { background: #2563eb; }
    .stat-card.red::before { background: #64748b; }
    .stat-card.kpi-card {
        isolation: isolate; border-color: var(--kpi-border); background: linear-gradient(135deg, #fff 0%, var(--kpi-soft) 100%);
        animation: kpi-card-enter .45s ease both;
    }
    .stat-card.kpi-card::before { height: 4px; background: var(--kpi-accent); }
    .stat-card.kpi-card::after {
        content: ''; position: absolute; z-index: -1; width: 115px; height: 115px; right: -48px; bottom: -64px;
        border-radius: 50%; background: var(--kpi-glow); filter: blur(3px);
    }
    .stat-card.kpi-card > * { position: relative; z-index: 1; }
    .kpi-respondents { --kpi-accent: #2563eb; --kpi-soft: #eff6ff; --kpi-border: #bfdbfe; --kpi-glow: rgba(37, 99, 235, .12); }
    .kpi-alumni { --kpi-accent: #7c3aed; --kpi-soft: #f5f3ff; --kpi-border: #ddd6fe; --kpi-glow: rgba(124, 58, 237, .12); }
    .kpi-response-minimum { --kpi-accent: #d97706; --kpi-soft: #fffbeb; --kpi-border: #fde68a; --kpi-glow: rgba(217, 119, 6, .12); }
    .kpi-index { --kpi-accent: #0f766e; --kpi-soft: #f0fdfa; --kpi-border: #99f6e4; --kpi-glow: rgba(15, 118, 110, .12); }
    .kpi-categories { --kpi-accent: #db2777; --kpi-soft: #fdf2f8; --kpi-border: #fbcfe8; --kpi-glow: rgba(219, 39, 119, .11); }
    .kpi-respondents .stat-icon-wrap { background: #dbeafe; color: #2563eb; }
    .kpi-alumni .stat-icon-wrap { background: #ede9fe; color: #7c3aed; }
    .kpi-response-minimum .stat-icon-wrap { background: #fef3c7; color: #b45309; }
    .kpi-index .stat-icon-wrap { background: #ccfbf1; color: #0f766e; }
    .kpi-categories .stat-icon-wrap { background: #fce7f3; color: #db2777; }
    .kpi-count, .kpi-inline-count { font-variant-numeric: tabular-nums; }
    .kpi-inline-count { font-weight: 700; color: var(--slate-900); }
    @keyframes kpi-card-enter { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    @media (prefers-reduced-motion: reduce) { .stat-card.kpi-card { animation: none; } }
    .stat-top { display: flex; justify-content: space-between; align-items: flex-start; gap: .75rem; margin-bottom: .7rem; }
    .stat-label {
        font-size: .7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .55px; color: var(--slate-500);
    }
    .stat-icon-wrap {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; background: var(--brand-50); color: var(--brand-500); flex-shrink: 0;
    }
    .stat-card.green .stat-icon-wrap { background: #f0fdfa; color: #0f766e; }
    .stat-card.amber .stat-icon-wrap { background: #eff6ff; color: #2563eb; }
    .stat-card.red .stat-icon-wrap { background: #f1f5f9; color: #475569; }
    .stat-value { font-size: 1.65rem; font-weight: 800; color: var(--slate-900); line-height: 1; }
    .stat-unit { font-size: .76rem; font-weight: 400; color: var(--slate-500); margin-left: .25rem; }
    .stat-name { font-size: .88rem; font-weight: 700; color: var(--slate-900); line-height: 1.35; margin-bottom: .22rem; overflow-wrap: anywhere; }
    .stat-sub { font-size: .72rem; color: var(--slate-500); margin-top: .35rem; line-height: 1.4; overflow-wrap: anywhere; }
    .stat-breakdown { display: flex; gap: .35rem; flex-wrap: wrap; margin-top: .55rem; }
    .stat-breakdown span { border-radius: 999px; padding: .18rem .45rem; font-size: .67rem; font-weight: 700; }
    .stat-breakdown .done { color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; }
    .stat-breakdown .pending { color: #b45309; background: #fffbeb; border: 1px solid #fde68a; }
    .stat-bar-track { height: 4px; background: var(--slate-100); border-radius: 99px; margin-top: .55rem; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 99px; background: #16a34a; }

    .chart-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .85rem; align-items: start; }
    @media(max-width:991px) { .chart-grid { grid-template-columns: 1fr; } }

    .panel-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: .75rem 1rem; border-bottom: 1px solid var(--slate-100); background: #fff;
        gap: .75rem; flex-wrap: wrap;
    }
    .panel-title { font-size: .86rem; font-weight: 700; color: var(--slate-700); line-height: 1.35; margin: 0; overflow-wrap: anywhere; }
    .panel-subtitle { font-size: .72rem; color: var(--slate-500); line-height: 1.4; margin: .08rem 0 0; overflow-wrap: anywhere; }
    .panel-body { padding: .75rem 1rem .9rem; }
    .chart-wrap { min-height: 0; }
    .chart-compact { height: 240px; }
    .chart-modal { min-height: 420px; }

    .feedback-list-main .fb-item:nth-child(n+4) { display: none; }
    .fb-item { padding: .75rem 1rem; border-bottom: 1px solid var(--slate-100); }
    .fb-item:last-child { border-bottom: none; }
    .fb-quote {
        font-size: .8rem; color: var(--slate-700); line-height: 1.5;
        font-style: italic; padding: .5rem .75rem;
        background: var(--brand-50); border-left: 3px solid var(--brand-500);
        border-radius: 0 8px 8px 0; margin-bottom: .45rem;
    }
    .fb-meta { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: .35rem; }
    .fb-who { font-size: .74rem; }
    .fb-who strong { color: var(--slate-700); font-weight: 600; }
    .fb-who span { color: var(--slate-500); }
    .fb-tag {
        font-size: .68rem; font-weight: 500; padding: .16rem .55rem;
        background: var(--brand-50); color: var(--brand-700);
        border: 1px solid var(--brand-100); border-radius: 99px;
    }
    .feedback-modal-body { max-height: 70vh; overflow-y: auto; padding: 0; }
    .pagination-nav { display: flex; align-items: center; justify-content: center; gap: .4rem; flex-wrap: wrap; }
    .modal-footer.pagination-nav { padding: .75rem 1rem; border-top: 1px solid var(--slate-100); }
    .pagination-button { min-width: 32px; padding: .3rem .5rem; border: 1px solid var(--slate-200); border-radius: 7px; background: #fff; color: var(--slate-700); font-size: .75rem; font-weight: 700; }
    .pagination-button:hover, .pagination-button.active { border-color: var(--brand-500); background: var(--brand-500); color: #fff; }
    .pagination-button:disabled { opacity: .45; cursor: not-allowed; }
    .pagination-ellipsis { min-width: 20px; color: var(--slate-500); font-size: .8rem; font-weight: 700; text-align: center; }

    .drill-card { padding: 1rem; display: grid; grid-template-columns: 220px 1fr; gap: 1rem; align-items: center; }
    @media(max-width:767px) { .drill-card { grid-template-columns: 1fr; } }
    .drill-score {
        min-height: 150px; border: 1px solid var(--slate-200); border-radius: 10px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: var(--slate-50); text-align: center; padding: 1rem;
    }
    .drill-score strong { font-size: 2rem; font-weight: 800; color: var(--slate-900); line-height: 1; }
    .drill-score span { font-size: .74rem; color: var(--slate-500); margin-top: .35rem; }
    .drill-title { font-size: .98rem; font-weight: 800; color: var(--slate-900); margin-bottom: .2rem; }
    .drill-subtitle { font-size: .74rem; color: var(--slate-500); margin-bottom: .85rem; }
    .rating-row { display: grid; grid-template-columns: 130px 1fr 62px; gap: .65rem; align-items: center; margin-bottom: .58rem; }
    .rating-label { font-size: .76rem; color: var(--slate-700); font-weight: 600; }
    .rating-track { height: 9px; background: var(--slate-100); border-radius: 99px; overflow: hidden; }
    .rating-fill { height: 100%; border-radius: 99px; width: 0; transition: width .22s ease; }
    .rating-fill.sb { background: #16a34a; }
    .rating-fill.b { background: #3b82f6; }
    .rating-fill.k { background: #d97706; }
    .rating-fill.sk { background: #dc2626; }
    .rating-value { font-size: .74rem; color: var(--slate-500); text-align: right; }
    .view-switch { display: inline-flex; border: 1px solid var(--slate-200); border-radius: 8px; overflow: hidden; }
    .view-switch button {
        border: 0; background: #fff; color: var(--slate-500);
        padding: .32rem .55rem; font-size: .74rem; font-weight: 700;
    }
    .view-switch button.active { background: var(--brand-500); color: #fff; }
    .kepuasan-chart-wrap { display: none; min-height: 280px; padding: .75rem 1rem 1rem; }
    .satisfaction-panel.chart-mode .kepuasan-table-wrap { display: none; }
    .satisfaction-panel.chart-mode .kepuasan-chart-wrap { display: block; }
    .tbl-kepuasan tbody tr { cursor: pointer; }
    .tbl-kepuasan tbody tr.active td { background: var(--brand-50); color: var(--brand-700) !important; }

    .satisfaction-panel, .satisfaction-panel * { color: #111 !important; }
    .satisfaction-panel small { color: var(--slate-500) !important; font-size: .68rem; white-space: nowrap; }
    .satisfaction-panel .view-switch button { color: var(--slate-500) !important; }
    .satisfaction-panel .view-switch button.active { color: #fff !important; background: var(--brand-500); }
    .satisfaction-panel .panel-header { border: 1px solid #d1d5db; border-width: 0 0 1px 0; background: #fff; }
    .tbl-kepuasan { width: 100%; border-collapse: collapse; font-size: .81rem; }
    .tbl-kepuasan thead th {
        padding: .56rem .9rem; font-size: .7rem; font-weight: 700;
        text-align: center; white-space: nowrap; letter-spacing: .3px;
        border-bottom: 1px solid #111; background: #fff;
    }
    .tbl-kepuasan thead .th-name { text-align: left; width: 26%; }
    .tbl-kepuasan tbody td {
        padding: .52rem .9rem; border-bottom: 1px solid var(--slate-100);
        text-align: center; vertical-align: middle;
    }
    .tbl-kepuasan tbody td:first-child { text-align: left; font-weight: 500; }
    .tbl-kepuasan tbody tr:nth-child(even) td { background: var(--slate-50); }
    .tbl-kepuasan tbody tr:hover td { background: #f3f4f6; transition: background .1s; }
    .tbl-kepuasan tfoot td {
        padding: .56rem .9rem; font-weight: 700; text-align: center;
        font-size: .8rem; border-top: 1px solid #111;
    }
    .tbl-kepuasan tfoot td:first-child { text-align: left; }

    .period-dashboard { border-color: var(--brand-100); }
    .period-dashboard .panel-header { background: linear-gradient(135deg, #f8fbff, #fff); }
    .period-score-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; padding: .9rem 1rem; }
    @media(max-width:767px) { .period-score-grid { grid-template-columns: 1fr; } }
    .period-score-card { border: 1px solid var(--slate-200); border-radius: 10px; padding: .8rem .9rem; background: #fff; }
    .period-score-card.recommended { border-color: var(--brand-100); background: var(--brand-50); }
    .period-score-label { display: block; color: var(--slate-500); font-size: .68rem; font-weight: 700; letter-spacing: .45px; text-transform: uppercase; }
    .period-score-value { display: block; margin-top: .32rem; color: var(--slate-900); font-size: 1.45rem; font-weight: 800; line-height: 1; }
    .period-score-note { display: block; color: var(--slate-500); font-size: .7rem; line-height: 1.45; margin-top: .35rem; }
    .period-content-grid { display: grid; grid-template-columns: minmax(260px, .8fr) minmax(0, 1.2fr); gap: .85rem; padding: 0 1rem 1rem; }
    .period-content-grid-clean { padding-top: 1rem; }
    .trend-card { background: linear-gradient(145deg, var(--brand-50), #fff); border-color: var(--brand-100); }
    @media(max-width:991px) { .period-content-grid { grid-template-columns: 1fr; } }
    .period-chart { height: 250px; }
    .period-table-section { padding-top: .15rem; }
    .period-table-section-header { margin-bottom: .65rem; }
    .period-table-section-header h6 { margin: 0; color: var(--slate-700); font-size: .82rem; font-weight: 700; }
    .period-table-section-header p { margin: .15rem 0 0; color: var(--slate-500); font-size: .72rem; }
    .period-table-wrap { overflow-x: auto; border: 1px solid var(--slate-100); border-radius: 9px; }
    .period-table { width: 100%; min-width: 570px; border-collapse: collapse; font-size: .76rem; }
    .period-table th { color: var(--slate-500); background: var(--slate-50); font-size: .66rem; font-weight: 700; letter-spacing: .3px; padding: .55rem .65rem; text-align: right; text-transform: uppercase; white-space: nowrap; }
    .period-table th.metric-col { min-width: 145px; white-space: normal; line-height: 1.35; }
    .period-table td { border-top: 1px solid var(--slate-100); color: var(--slate-700); padding: .55rem .65rem; text-align: right; white-space: nowrap; }
    .period-table th:first-child, .period-table td:first-child { text-align: left; font-weight: 700; }
    .period-table tbody tr:hover td { background: var(--slate-50); }
    .period-table-footer { display: flex; align-items: center; justify-content: space-between; gap: .65rem; padding: .65rem .75rem; border-top: 1px solid var(--slate-100); background: #fff; }
    .period-table-footer span { color: var(--slate-500); font-size: .7rem; }
    .period-modal-body { max-height: 70vh; overflow: auto; padding: 0; }
    .category-period-comparison { border-color: var(--brand-100); }
    .category-comparison-chart-wrap { min-height: 320px; padding: 1rem; }
    .category-comparison-table-wrap { overflow-x: auto; border-top: 1px solid var(--slate-100); }
    .category-comparison-table { width: 100%; min-width: 700px; border-collapse: collapse; font-size: .78rem; }
    .category-comparison-table th { padding: .65rem .75rem; background: var(--slate-50); color: var(--slate-500); font-size: .68rem; font-weight: 700; letter-spacing: .3px; text-align: center; text-transform: uppercase; white-space: nowrap; }
    .category-comparison-table th:first-child, .category-comparison-table td:first-child { min-width: 190px; text-align: left; }
    .category-comparison-table td { padding: .6rem .75rem; border-top: 1px solid var(--slate-100); color: var(--slate-700); text-align: center; }
    .category-comparison-table td:first-child { color: var(--slate-900); font-weight: 600; }
    .category-comparison-table tbody tr:hover td { background: var(--slate-50); }
    .period-category-list { display: grid; gap: .65rem; padding: 0 1rem 1rem; }
    .period-detail { border: 1px solid var(--slate-200); border-radius: 10px; overflow: hidden; }
    .period-detail summary { cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: .75rem; list-style: none; padding: .7rem .85rem; background: #fff; }
    .period-detail summary::-webkit-details-marker { display: none; }
    .period-detail summary::after { content: '\F282'; font-family: bootstrap-icons; color: var(--slate-500); font-size: .72rem; }
    .period-detail[open] summary::after { content: '\F286'; }
    .period-detail[open] summary { border-bottom: 1px solid var(--slate-100); background: var(--slate-50); }
    .period-detail-title { color: var(--slate-900); font-size: .8rem; font-weight: 700; }
    .period-detail-meta { color: var(--slate-500); font-size: .7rem; margin-top: .14rem; }
    .period-category-table { width: 100%; border-collapse: collapse; font-size: .75rem; }
    .period-category-table th { background: #fff; color: var(--slate-500); font-size: .65rem; font-weight: 700; letter-spacing: .3px; padding: .5rem .85rem; text-align: left; text-transform: uppercase; }
    .period-category-table th:not(:first-child), .period-category-table td:not(:first-child) { text-align: right; }
    .period-category-table td { border-top: 1px solid var(--slate-100); color: var(--slate-700); padding: .5rem .85rem; }
    .period-category-table td:first-child { font-weight: 600; }
    .dashboard-info-list { display: grid; gap: .7rem; padding-left: 1.15rem; margin: 0; }
    .dashboard-info-list li { color: var(--slate-700); font-size: .82rem; line-height: 1.55; }
    .dashboard-info-formula { background: var(--slate-50); border: 1px solid var(--slate-200); border-radius: 8px; color: var(--slate-700); font-size: .78rem; line-height: 1.55; padding: .7rem .8rem; }
    .empty-state { text-align: center; padding: 1.75rem 1rem; color: var(--slate-500); font-size: .84rem; }
    .empty-state i { display: block; font-size: 1.5rem; margin-bottom: .45rem; opacity: .45; }

    .modal-content { border: 0; border-radius: var(--radius); overflow: hidden; }
    .modal-header { border-bottom-color: var(--slate-100); padding: .85rem 1rem; }
    .modal-title { font-size: .95rem; font-weight: 700; color: var(--slate-900); }
</style>

@php
    $activePeriode = $filters['periode'] ?? [];
    $selectedProdi = $filters['program_studi'] ?? [];
    $availableProdi = $filterOptions['prodiList'];
    $pct = min(100, round((($rataKeseluruhan ?? 0) / 4) * 100));
    $activeKategori = $kategoriTerlemah->kategori ?? $kategoriTerbaik->kategori ?? null;
@endphp

<div class="db-wrap">
    <div class="db-header">
        <div>
            <h3>Dashboard Evaluasi Lulusan</h3>
            <p>Ringkasan performa dan kualitas lulusan Universitas Dinamika di dunia kerja.</p>
        </div>
        <div class="dashboard-header-actions">
            <button type="button" class="btn-extend" data-bs-toggle="modal" data-bs-target="#dashboardInfoModal">
                <i class="bi bi-exclamation-circle"></i> Cara baca dashboard
            </button>
        <form method="GET" action="{{ route('dashboard') }}" id="filterForm" class="dashboard-filter-form">
            <div class="dashboard-filter-field">
                <label class="form-label">Periode</label>
                <div class="dropdown periode-dropdown">
                        <button class="periode-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false">
                            <span>
                                @if(empty($activePeriode))
                                    Semua Periode
                                @elseif(count($activePeriode) === 1)
                                    {{ $filterOptions['periodeList'][$activePeriode[0]] ?? $activePeriode[0] }}
                                @else
                                    {{ count($activePeriode) }} periode dipilih
                                @endif
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu periode-menu">
                            @foreach($filterOptions['periodeList'] as $kodePeriode => $namaPeriode)
                                <label class="periode-option">
                                    <input type="checkbox" name="periode[]" value="{{ $kodePeriode }}" {{ in_array($kodePeriode, $activePeriode, true) ? 'checked' : '' }}>
                                    <span>{{ $namaPeriode }}</span>
                                </label>
                            @endforeach
                        </div>
                </div>
            </div>
            <div class="dashboard-filter-field prodi">
                <label class="form-label">Program Studi</label>
                <div class="dropdown periode-dropdown">
                    <button class="periode-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="false" aria-expanded="false">
                        <span>
                            @if(empty($selectedProdi))
                                Semua prodi
                            @elseif(count($selectedProdi) === 1)
                                {{ $selectedProdi[0] }}
                            @else
                                {{ count($selectedProdi) }} prodi dipilih
                            @endif
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu periode-menu">
                        @foreach($availableProdi as $prodi)
                            <label class="periode-option">
                                <input type="checkbox" name="program_studi[]" value="{{ $prodi }}" {{ in_array($prodi, $selectedProdi, true) ? 'checked' : '' }}>
                                <span>{{ $prodi }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="dashboard-filter-actions">
                <button type="submit" class="btn btn-apply"><i class="bi bi-funnel me-1"></i>Terapkan filter</button>
                <a href="{{ route('dashboard') }}" class="btn btn-reset btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset filter</a>
            </div>
        </form>
        </div>
    </div>

    <div class="stat-grid">
        @if($isAdmin)
        <div class="stat-card kpi-card kpi-respondents">
            <div class="stat-top">
                <span class="stat-label">Jumlah Responden yang Mengisi</span>
                <div class="stat-icon-wrap"><i class="bi bi-people-fill"></i></div>
            </div>
            <span class="stat-value kpi-count" data-count-up data-count="{{ $totalResponden ?? 0 }}" data-count-decimals="0" aria-live="polite">{{ $totalResponden ?? 0 }}</span>
            <span class="stat-unit">responden</span>
            <div class="stat-breakdown">
                <span class="done"><i class="bi bi-check-circle-fill"></i> {{ $totalResponden ?? 0 }} sudah mengisi</span>
                <span class="pending"><i class="bi bi-clock-history"></i> {{ $respondenBelumMengisi ?? 0 }} belum mengisi</span>
            </div>
        </div>

        <div class="stat-card kpi-card kpi-alumni">
            <div class="stat-top">
                <span class="stat-label">Jumlah Alumni yang Dinilai</span>
                <div class="stat-icon-wrap"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <span class="stat-value kpi-count" data-count-up data-count="{{ $totalLulusan ?? 0 }}" data-count-decimals="0" aria-live="polite">{{ $totalLulusan ?? 0 }}</span>
            <span class="stat-unit">alumni</span>
            <div class="stat-sub">Lulusan dalam cakupan filter survei</div>
        </div>

        <div class="stat-card kpi-card kpi-response-minimum">
            <div class="stat-top">
                <span class="stat-label">Response Rate Minimum</span>
                <div class="stat-icon-wrap"><i class="bi bi-bullseye"></i></div>
            </div>
            <span class="stat-value kpi-count" data-count-up data-count="{{ $skorKepuasan['minimum_response_rate_pct'] ?? 0 }}" data-count-decimals="1" aria-live="polite">{{ number_format($skorKepuasan['minimum_response_rate_pct'] ?? 0, 1) }}</span>
            <span class="stat-unit">%</span>
            <div class="stat-sub">Target minimal {{ number_format($skorKepuasan['minimum_response_count'] ?? 0) }} responden dari {{ number_format($totalLulusan ?? 0) }} alumni pada filter aktif.</div>
        </div>
        @endif

        <div class="stat-card kpi-card kpi-index">
            <div class="stat-top">
                <span class="stat-label">Indeks Kepuasan Pengguna</span>
                <div class="stat-icon-wrap"><i class="bi bi-star-fill"></i></div>
            </div>
            <span class="stat-value kpi-count" data-count-up data-count="{{ $rataKeseluruhan ?? 0 }}" data-count-decimals="2" aria-live="polite">{{ number_format($rataKeseluruhan ?? 0, 2) }}</span>
            <span class="stat-unit">/ 4.00</span>
            <div class="stat-bar-track">
                <div class="stat-bar-fill" style="width:{{ $pct }}%;"></div>
            </div>
            <div class="stat-sub">Skor murni {{ number_format($skorKepuasan['skor_murni'] ?? 0, 2) }} × faktor {{ number_format($skorKepuasan['faktor_pembobot'] ?? 0, 2) }}</div>
        </div>

        <div class="stat-card kpi-card kpi-categories">
            <div class="stat-top">
                <span class="stat-label">Kategori Terbaik &amp; Terendah</span>
                <div class="stat-icon-wrap"><i class="bi bi-trophy-fill"></i></div>
            </div>
            <div class="stat-sub"><strong>Terbaik:</strong> {{ $kategoriTerbaik->kategori ?? '-' }} (<span class="kpi-inline-count" data-count-up data-count="{{ $kategoriTerbaik->rata_rata ?? 0 }}" data-count-decimals="2">{{ number_format($kategoriTerbaik->rata_rata ?? 0, 2) }}</span> / 4.00)</div>
            <div class="stat-sub"><strong>Terendah:</strong> {{ $kategoriTerlemah->kategori ?? '-' }} (<span class="kpi-inline-count" data-count-up data-count="{{ $kategoriTerlemah->rata_rata ?? 0 }}" data-count-decimals="2">{{ number_format($kategoriTerlemah->rata_rata ?? 0, 2) }}</span> / 4.00)</div>
        </div>
    </div>

    @include('admin.dashboard.partials.period-dashboard')

    @include('admin.dashboard.partials.category-period-comparison')

    @include('admin.dashboard.partials.satisfaction-panel')

    <div class="chart-grid">
        @if($isAdmin)
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h6 class="panel-title" id="prodiPanelTitle">Responden Berdasarkan Program Studi</h6>
                    <p class="panel-subtitle" id="prodiPanelSubtitle">Ringkasan jumlah responden menurut program studi</p>
                </div>
            </div>
            <div class="panel-body">
                <div id="chart-prodi" class="chart-wrap chart-compact"></div>
            </div>
        </div>
        @endif

        <div class="panel">
            <div class="panel-header">
                <div>
                    <h6 class="panel-title" id="kinerjaPanelTitle">Skor Kepuasan Terkonversi per Kategori</h6>
                    <p class="panel-subtitle" id="kinerjaPanelSubtitle">Ringkasan skor kepuasan untuk setiap kategori</p>
                </div>
            </div>
            <div class="panel-body">
                <div id="chart-kinerja" class="chart-wrap chart-compact"></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h6 class="panel-title">Umpan Balik Terbaru</h6>
                <p class="panel-subtitle">Tiga feedback terbaru ditampilkan di dashboard utama</p>
            </div>
            <button type="button" class="btn-extend" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                <i class="bi bi-chat-square-text"></i> Lihat Semua
            </button>
        </div>
        <div class="feedback-list-main">
            @forelse($komentarTerbaru->take(3) as $komen)
                @include('admin.dashboard.partials.feedback-item', ['komen' => $komen])
            @empty
                <div class="empty-state"><i class="bi bi-chat-dots"></i>Belum ada umpan balik.</div>
            @endforelse
        </div>
    </div>

</div>

<div class="modal fade" id="dashboardInfoModal" tabindex="-1" aria-labelledby="dashboardInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dashboardInfoModalLabel"><i class="bi bi-info-circle me-1 text-primary"></i> Cara membaca dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3 text-muted" style="font-size:.84rem;">Dashboard ini membantu melihat penilaian perusahaan terhadap lulusan. Gunakan filter di bagian atas bila ingin melihat data tertentu.</p>
                <ol class="dashboard-info-list">
                    <li><strong>Dari mana datanya?</strong> Angka di dashboard berasal dari survei yang sudah dikirim oleh perusahaan. Setelah survei selesai, jawabannya menjadi arsip sehingga data lama tetap sama meskipun pertanyaan atau profil perusahaan diperbarui.</li>
                    <li><strong>Arti jumlah responden dan jumlah alumni.</strong> Jumlah responden adalah perusahaan atau penyelia unik yang mengisi survei. Kartu ini juga menunjukkan yang sudah dan belum mengisi. Jumlah alumni adalah lulusan yang mendapat sesi survei pada filter yang dipilih.</li>
                    <li><strong>Cara nilai dihitung.</strong> Jawaban penilaian memakai angka 1 sampai 4, lalu sistem mencari nilai rata-ratanya. Nama pilihan jawaban boleh berbeda pada tiap periode, tetapi angka nilainya tetap dipakai agar hasilnya konsisten.</li>
                </ol>
                <div class="dashboard-info-formula my-3">
                    <strong>Ringkasnya:</strong> skor murni adalah rata-rata nilai jawaban. Skor akhir adalah skor murni yang disesuaikan dengan tingkat respons.<br>
                    Sistem membandingkan jumlah responden yang mengisi dengan jumlah alumni yang dinilai. Jika jumlah respons masih sedikit, nilai akhir ikut disesuaikan agar hasilnya lebih adil.<br>
                    Target tingkat respons minimum mengikuti jumlah alumni dalam filter: 20% - (10% / 5.000 × jumlah alumni) untuk jumlah alumni di bawah 5.000, atau 10% untuk 5.000 alumni atau lebih.
                </div>
                <ol class="dashboard-info-list" start="4">
                    <li><strong>Indeks Kepuasan Pengguna.</strong> Nilai ini memakai jawaban penilaian dari 1 sampai 4. Semakin dekat ke 4, semakin baik penilaian pengguna lulusan. Gunakan tren periode untuk melihat perubahan dari tahun ke tahun.</li>
                    <li><strong>Grafik dan tabel.</strong> Semua bagian mengikuti filter aktif. Grafik kategori membantu menemukan kekuatan dan area yang perlu ditingkatkan; klik batang kategori untuk melihat rincian jawabannya.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

{{-- Tampilan detail grafik dihapus karena isinya mengulang data ringkasan. --}}
{{-- <div class="modal fade" id="prodiChartModal" tabindex="-1" aria-labelledby="prodiChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="prodiChartModalLabel">Responden Berdasarkan Program Studi</h5>
                <div class="panel-actions ms-auto">
                    <button type="button" class="btn-back" id="backProdiFullChart">
                        <i class="bi bi-arrow-left"></i> Ringkasan
                    </button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chart-prodi-full" class="chart-modal"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kinerjaChartModal" tabindex="-1" aria-labelledby="kinerjaChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kinerjaChartModalLabel">Skor Kepuasan Terkonversi per Kategori</h5>
                <div class="panel-actions ms-auto">
                    <button type="button" class="btn-back" id="backKinerjaFullChart">
                        <i class="bi bi-arrow-left"></i> Ringkasan
                    </button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="chart-kinerja-full" class="chart-modal"></div>
            </div>
        </div>
    </div>
</div> --}}

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalLabel">Seluruh Umpan Balik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body feedback-modal-body" id="feedbackModalBody">
                @forelse($komentarTerbaru as $komen)
                    <div data-feedback-item>
                        @include('admin.dashboard.partials.feedback-item', ['komen' => $komen])
                    </div>
                @empty
                    <div class="empty-state"><i class="bi bi-chat-dots"></i>Belum ada umpan balik.</div>
                @endforelse
            </div>
            @if($komentarTerbaru->count() > 10)
                <div class="modal-footer pagination-nav" id="feedbackPagination"></div>
            @endif
        </div>
    </div>
</div>

<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script>
    const chartData = @json($chartData);
    const chartLabels = @json($chartLabels);
    const periodTrendLabels = @json($periodTrendLabels);
    const periodTrendData = @json($periodTrendData);
    const categoryComparisonPeriods = @json($categoryComparisonPeriods);
    const categoryPeriodComparison = @json($categoryPeriodComparison);
    const isAdmin = @json($isAdmin);
    const respondenProdiData = @json($respondenProdiData);
    const respondenProdiLabels = @json($respondenProdiLabels);
    const prodiDetails = @json($prodiDetails);
    const kategoriDetails = @json($kategoriDetails);
    const initialKategori = @json($activeKategori);
    const compactLimit = 6;
    const periodTrendLimit = 6;
    const periodTrendPreviewLabels = periodTrendLabels.slice(-periodTrendLimit);
    const periodTrendPreviewData = periodTrendData.slice(-periodTrendLimit);
    const kategoriDetailMap = new Map(kategoriDetails.map((item) => [item.kategori, item]));
    const prodiDetailMap = new Map(prodiDetails.map((item) => [item.prodi, item]));

    const sliceData = (labels, data, limit = compactLimit) => ({
        labels: labels.slice(0, limit),
        data: data.slice(0, limit)
    });

    const animateKpiCounts = () => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        document.querySelectorAll('[data-count-up]').forEach((element) => {
            const target = Number(element.dataset.count || 0);
            const decimals = Number(element.dataset.countDecimals || 0);
            const format = (value) => Number(value).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals,
            });

            if (reduceMotion || !Number.isFinite(target)) {
                element.textContent = format(target);
                return;
            }

            const duration = 700;
            const startedAt = performance.now();
            const update = (now) => {
                const progress = Math.min((now - startedAt) / duration, 1);
                const easedProgress = 1 - Math.pow(1 - progress, 3);
                element.textContent = format(target * easedProgress);

                if (progress < 1) requestAnimationFrame(update);
            };

            requestAnimationFrame(update);
        });
    };

    animateKpiCounts();

    const emptyChart = (selector, message) => {
        const target = document.querySelector(selector);
        if (target) target.innerHTML = `<div class="empty-state"><i class="bi bi-bar-chart"></i>${message}</div>`;
    };

    const chartHeight = (count, min = 220, row = 34, max = 420) => Math.min(max, Math.max(min, count * row + 46));
    const formatPct = (value) => `${Number(value || 0).toFixed(1)}%`;
    const ratingLabels = {
        sb: 'Sangat Baik',
        b: 'Baik',
        k: 'Cukup',
        sk: 'Kurang'
    };

    const setActiveKategori = (kategori) => {
        const detail = kategoriDetailMap.get(kategori) || kategoriDetails[0];
        if (!detail) return;

        document.querySelectorAll('[data-kategori-row]').forEach((row) => {
            row.classList.toggle('active', row.dataset.kategoriRow === detail.kategori);
        });
    };

    const setPanelText = (titleId, subtitleId, title, subtitle) => {
        const titleEl = document.getElementById(titleId);
        const subtitleEl = document.getElementById(subtitleId);

        if (titleEl) titleEl.textContent = title;
        if (subtitleEl) subtitleEl.textContent = subtitle;
    };

    const setBackVisible = (buttonId, visible) => {
        document.getElementById(buttonId)?.classList.toggle('show', visible);
    };

    const prodiOptions = (labels, data, expanded = false, onSelect = null) => ({
        series: [{ name: 'Responden', data }],
        chart: {
            type: 'bar',
            height: expanded ? chartHeight(labels.length, 320, 38, 760) : 240,
            toolbar: { show: false },
            fontFamily: 'inherit',
            events: onSelect ? {
                dataPointSelection: (event, chartContext, config) => {
                    const prodi = labels[config.dataPointIndex];
                    if (prodi) onSelect(prodi);
                }
            } : {}
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 5,
                barHeight: expanded ? '54%' : '48%',
                dataLabels: { position: 'center' }
            }
        },
        colors: ['#2563eb'],
        dataLabels: {
            enabled: true,
            formatter: value => `${value}`,
            style: { colors: ['#fff'], fontSize: '11px', fontWeight: 700 }
        },
        xaxis: {
            categories: labels,
            decimalsInFloat: 0,
            labels: {
                trim: true,
                style: { colors: '#64748b', fontSize: '11px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { title: { formatter: () => 'Responden:' } } }
    });

    const prodiDetailOptions = (detail, expanded = false) => {
        const items = detail.jenis_perusahaan || [];
        const labels = items.map((item) => item.label);
        const data = items.map((item) => item.total);

        return {
            series: [{ name: 'Responden', data }],
            chart: { type: 'bar', height: expanded ? 420 : 240, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { horizontal: false, borderRadius: 5, columnWidth: expanded ? '36%' : '45%', dataLabels: { position: 'top' } } },
            colors: ['#3b82f6'],
            dataLabels: {
                enabled: true,
                offsetY: -18,
                style: { colors: ['#334155'], fontSize: '11px', fontWeight: 700 },
                formatter: value => `${value}`
            },
            xaxis: {
                categories: labels,
                labels: { trim: true, rotate: expanded ? -30 : -20, hideOverlappingLabels: true, style: { colors: '#64748b', fontSize: expanded ? '11px' : '10px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                decimalsInFloat: 0,
                labels: { formatter: value => Number(value).toFixed(0), style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { y: { title: { formatter: () => 'Responden:' } } }
        };
    };

    const kinerjaOptions = (labels, data, expanded = false, onSelect = null) => ({
        series: [{ name: 'Skor', data }],
        chart: {
            type: 'bar',
            height: expanded ? 420 : 240,
            toolbar: { show: false },
            fontFamily: 'inherit',
            events: onSelect ? {
                dataPointSelection: (event, chartContext, config) => {
                    const kategori = labels[config.dataPointIndex];
                    if (kategori) onSelect(kategori);
                }
            } : {}
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 5,
                columnWidth: expanded ? '44%' : '48%',
                dataLabels: { position: 'top' }
            }
        },
        colors: ['#2563eb'],
        dataLabels: {
            enabled: true,
            offsetY: -18,
            style: { colors: ['#334155'], fontSize: '11px', fontWeight: 700 },
            formatter: value => Number(value).toFixed(2)
        },
        xaxis: {
            categories: labels,
            labels: {
                trim: true,
                rotate: expanded ? -35 : -25,
                hideOverlappingLabels: true,
                style: { colors: '#64748b', fontSize: expanded ? '11px' : '10px' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            max: 4,
            tickAmount: 4,
            labels: {
                formatter: value => Number(value).toFixed(0),
                style: { colors: '#334155', fontSize: '11px', fontWeight: 500 }
            }
        },
        grid: { yaxis: { lines: { show: true } }, borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { title: { formatter: () => 'Skor:' } } }
    });

    const periodTrendOptions = () => ({
        series: [{ name: 'Skor akhir', data: periodTrendPreviewData }],
        chart: { type: 'line', height: 250, toolbar: { show: false }, fontFamily: 'inherit' },
        stroke: { width: 3, curve: 'smooth' },
        markers: { size: 5, strokeWidth: 3, hover: { size: 7 } },
        colors: ['#2563eb'],
        dataLabels: {
            enabled: true,
            offsetY: -8,
            formatter: value => Number(value).toFixed(2),
            style: { colors: ['#334155'], fontSize: '10px', fontWeight: 700 }
        },
        xaxis: {
            categories: periodTrendPreviewLabels,
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            max: 4,
            tickAmount: 4,
            labels: { formatter: value => Number(value).toFixed(0), style: { colors: '#64748b', fontSize: '11px' } }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { formatter: value => `${Number(value).toFixed(2)} / 4.00` } }
    });

    const categoryPeriodComparisonOptions = () => ({
        series: categoryPeriodComparison.map((item) => ({
            name: item.kategori,
            data: item.scores.map((score, index) => ({ x: categoryComparisonPeriods[index].label, y: score }))
        })),
        chart: {
            type: 'heatmap',
            height: Math.min(560, Math.max(320, categoryPeriodComparison.length * 42 + 90)),
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        dataLabels: {
            enabled: true,
            formatter: value => value === null ? '-' : Number(value).toFixed(2),
            style: { colors: ['#fff'], fontSize: '10px', fontWeight: 700 }
        },
        plotOptions: {
            heatmap: {
                radius: 4,
                colorScale: {
                    ranges: [
                        { from: 0, to: 2, color: '#dc2626', name: 'Kurang' },
                        { from: 2.01, to: 3, color: '#d97706', name: 'Cukup' },
                        { from: 3.01, to: 3.49, color: '#2563eb', name: 'Baik' },
                        { from: 3.5, to: 4, color: '#16a34a', name: 'Sangat Baik' },
                    ]
                }
            }
        },
        xaxis: {
            labels: { trim: true, style: { colors: '#64748b', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 600 } } },
        legend: { position: 'top', fontSize: '12px', markers: { radius: 4 } },
        grid: { padding: { right: 10 } },
        tooltip: { y: { formatter: value => value === null ? 'Tidak ada data' : `${Number(value).toFixed(2)} / 4.00` } }
    });

    const kategoriDetailOptions = (detail, expanded = false) => {
        const keys = ['sb', 'b', 'k', 'sk'];

        return {
            series: [{ name: 'Persentase', data: keys.map((key) => detail.percentages?.[key] || 0) }],
            chart: { type: 'bar', height: expanded ? 420 : 240, toolbar: { show: false }, fontFamily: 'inherit' },
            plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: expanded ? '46%' : '52%', dataLabels: { position: 'center' } } },
            colors: ['#16a34a'],
            dataLabels: {
                enabled: true,
                formatter: (value, opts) => {
                    const key = keys[opts.dataPointIndex];
                    return isAdmin ? `${formatPct(value)} (${detail.counts?.[key] || 0})` : formatPct(value);
                },
                style: { colors: ['#fff'], fontSize: '11px', fontWeight: 700 }
            },
            xaxis: {
                min: 0,
                max: 100,
                categories: keys.map((key) => ratingLabels[key]),
                labels: { formatter: (value) => `${Number(value).toFixed(0)}%`, style: { colors: '#64748b', fontSize: '11px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 600 } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { y: { formatter: (value) => formatPct(value), title: { formatter: () => 'Porsi:' } } }
        };
    };

    const kepuasanStackOptions = () => ({
        series: [
            { name: 'Sangat Baik', data: kategoriDetails.map((item) => item.percentages.sb) },
            { name: 'Baik', data: kategoriDetails.map((item) => item.percentages.b) },
            { name: 'Cukup', data: kategoriDetails.map((item) => item.percentages.k) },
            { name: 'Kurang', data: kategoriDetails.map((item) => item.percentages.sk) },
        ],
        chart: { type: 'bar', height: 300, stacked: true, stackType: '100%', toolbar: { show: false }, fontFamily: 'inherit' },
        plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '58%' } },
        colors: ['#16a34a', '#3b82f6', '#d97706', '#dc2626'],
        xaxis: {
            categories: kategoriDetails.map((item) => item.kategori),
            labels: { formatter: (value) => `${Number(value).toFixed(0)}%`, style: { colors: '#64748b', fontSize: '11px' } }
        },
        yaxis: { labels: { style: { colors: '#334155', fontSize: '11px', fontWeight: 500 } } },
        dataLabels: { enabled: false },
        legend: { position: 'top', fontSize: '12px', markers: { radius: 4 } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        tooltip: { y: { formatter: (value) => formatPct(value) } }
    });

    let prodiChart = null;
    let kinerjaChart = null;
    let prodiFullChart = null;
    let kinerjaFullChart = null;
    let kepuasanStackChart = null;
    let periodTrendChart = null;
    let categoryPeriodComparisonChart = null;
    const feedbackPageSize = 10;
    const compactProdi = sliceData(respondenProdiLabels, respondenProdiData);
    const compactKinerja = sliceData(chartLabels, chartData);

    setActiveKategori(initialKategori);

    const renderChart = (chart, selector, options) => {
        if (chart) chart.destroy();

        const target = document.querySelector(selector);
        if (!target) return null;

        target.innerHTML = '';
        const nextChart = new ApexCharts(target, options);
        nextChart.render();

        return nextChart;
    };

    const renderProdiSummary = () => {
        setPanelText(
            'prodiPanelTitle',
            'prodiPanelSubtitle',
            'Responden Berdasarkan Program Studi',
            'Ringkasan jumlah responden menurut program studi'
        );

        if (compactProdi.data.length) {
            prodiChart = renderChart(prodiChart, '#chart-prodi', prodiOptions(compactProdi.labels, compactProdi.data));
        } else {
            emptyChart('#chart-prodi', 'Belum ada data responden per Prodi.');
        }
    };

    const renderProdiDetail = (prodi) => {
        const detail = prodiDetailMap.get(prodi);
        if (!detail) return;

        setPanelText(
            'prodiPanelTitle',
            'prodiPanelSubtitle',
            `Detail ${detail.prodi}`,
            `${detail.total} responden | Fakultas: ${detail.fakultas}`
        );
        setBackVisible('backProdiChart', true);
        prodiChart = renderChart(prodiChart, '#chart-prodi', prodiDetailOptions(detail));
    };

    const renderKinerjaSummary = () => {
        setPanelText(
            'kinerjaPanelTitle',
            'kinerjaPanelSubtitle',
            'Skor Kepuasan Terkonversi per Kategori',
            'Ringkasan skor kepuasan untuk setiap kategori'
        );

        if (compactKinerja.data.length) {
            kinerjaChart = renderChart(kinerjaChart, '#chart-kinerja', kinerjaOptions(compactKinerja.labels, compactKinerja.data));
        } else {
            emptyChart('#chart-kinerja', 'Belum ada data penilaian.');
        }
    };

    const renderKategoriDetail = (kategori) => {
        const detail = kategoriDetailMap.get(kategori);
        if (!detail) return;

        setActiveKategori(kategori);
        setPanelText(
            'kinerjaPanelTitle',
            'kinerjaPanelSubtitle',
            `Detail ${detail.kategori}`,
            isAdmin
                ? `Skor rata-rata ${Number(detail.rata_rata || 0).toFixed(2)} dari ${detail.total_respon || 0} respon penilaian`
                : `Skor rata-rata ${Number(detail.rata_rata || 0).toFixed(2)} / 4.00`
        );
        setBackVisible('backKinerjaChart', true);
        kinerjaChart = renderChart(kinerjaChart, '#chart-kinerja', kategoriDetailOptions(detail));
    };

    const renderProdiFullSummary = () => {
        const title = document.getElementById('prodiChartModalLabel');
        if (title) title.textContent = 'Responden Berdasarkan Program Studi';
        setBackVisible('backProdiFullChart', false);

        if (respondenProdiData.length) {
            prodiFullChart = renderChart(
                prodiFullChart,
                '#chart-prodi-full',
                prodiOptions(respondenProdiLabels, respondenProdiData, true, renderProdiFullDetail)
            );
        } else {
            emptyChart('#chart-prodi-full', 'Belum ada data responden per Prodi.');
        }
    };

    const renderProdiFullDetail = (prodi) => {
        const detail = prodiDetailMap.get(prodi);
        if (!detail) return;

        const title = document.getElementById('prodiChartModalLabel');
        if (title) title.textContent = `Detail ${detail.prodi} - ${detail.total} responden`;
        setBackVisible('backProdiFullChart', true);
        prodiFullChart = renderChart(prodiFullChart, '#chart-prodi-full', prodiDetailOptions(detail, true));
    };

    const renderKinerjaFullSummary = () => {
        const title = document.getElementById('kinerjaChartModalLabel');
        if (title) title.textContent = 'Skor Kepuasan Terkonversi per Kategori';
        setBackVisible('backKinerjaFullChart', false);

        if (chartData.length) {
            kinerjaFullChart = renderChart(
                kinerjaFullChart,
                '#chart-kinerja-full',
                kinerjaOptions(chartLabels, chartData, true, renderKinerjaFullDetail)
            );
        } else {
            emptyChart('#chart-kinerja-full', 'Belum ada data penilaian.');
        }
    };

    const renderKinerjaFullDetail = (kategori) => {
        const detail = kategoriDetailMap.get(kategori);
        if (!detail) return;

        setActiveKategori(kategori);
        const title = document.getElementById('kinerjaChartModalLabel');
        if (title) title.textContent = `Detail ${detail.kategori} - skor ${Number(detail.rata_rata || 0).toFixed(2)}`;
        setBackVisible('backKinerjaFullChart', true);
        kinerjaFullChart = renderChart(kinerjaFullChart, '#chart-kinerja-full', kategoriDetailOptions(detail, true));
    };

    renderProdiSummary();
    renderKinerjaSummary();

    if (periodTrendPreviewData.length) {
        periodTrendChart = renderChart(periodTrendChart, '#chart-period-trend', periodTrendOptions());
    } else {
        emptyChart('#chart-period-trend', 'Belum ada periode yang dapat dibandingkan.');
    }

    if (categoryComparisonPeriods.length > 1 && categoryPeriodComparison.length) {
        categoryPeriodComparisonChart = renderChart(
            categoryPeriodComparisonChart,
            '#chart-category-period-comparison',
            categoryPeriodComparisonOptions()
        );
    }

    const feedbackItems = [...document.querySelectorAll('[data-feedback-item]')];
    const feedbackPagination = document.getElementById('feedbackPagination');
    const paginationMarkup = (currentPage, totalPages, pageAttribute) => {
        const pageNumbers = [...new Set([1, currentPage - 1, currentPage, currentPage + 1, totalPages]
            .filter((page) => page >= 1 && page <= totalPages))].sort((a, b) => a - b);
        const pageItems = pageNumbers.flatMap((page, index) => {
            const previous = pageNumbers[index - 1];
            return index > 0 && page - previous > 1 ? ['ellipsis', page] : [page];
        });

        return `
            <button type="button" class="pagination-button" ${pageAttribute}="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''} aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left"></i></button>
            ${pageItems.map((item) => item === 'ellipsis'
                ? '<span class="pagination-ellipsis" aria-hidden="true">&hellip;</span>'
                : `<button type="button" class="pagination-button ${item === currentPage ? 'active' : ''}" ${pageAttribute}="${item}" aria-label="Halaman ${item}">${item}</button>`
            ).join('')}
            <button type="button" class="pagination-button" ${pageAttribute}="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''} aria-label="Halaman berikutnya"><i class="bi bi-chevron-right"></i></button>`;
    };

    const renderFeedbackPage = (page = 1) => {
        const totalPages = Math.ceil(feedbackItems.length / feedbackPageSize);
        const safePage = Math.min(Math.max(page, 1), totalPages || 1);

        feedbackItems.forEach((item, index) => {
            item.hidden = index < (safePage - 1) * feedbackPageSize || index >= safePage * feedbackPageSize;
        });

        if (!feedbackPagination) return;

        feedbackPagination.innerHTML = paginationMarkup(safePage, totalPages, 'data-feedback-page');
    };

    feedbackPagination?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-feedback-page]');
        if (button && !button.disabled) renderFeedbackPage(Number(button.dataset.feedbackPage));
    });
    document.getElementById('feedbackModal')?.addEventListener('shown.bs.modal', () => renderFeedbackPage(1));

    const periodRows = [...document.querySelectorAll('[data-period-row]')];
    const periodPagination = document.getElementById('periodPagination');
    const periodPaginationSummary = document.getElementById('periodPaginationSummary');
    const periodPageSize = 6;
    const renderPeriodPage = (page = 1) => {
        const totalPages = Math.ceil(periodRows.length / periodPageSize);
        const safePage = Math.min(Math.max(page, 1), totalPages || 1);
        const firstItem = (safePage - 1) * periodPageSize;
        const lastItem = Math.min(firstItem + periodPageSize, periodRows.length);

        periodRows.forEach((row, index) => {
            row.hidden = index < firstItem || index >= lastItem;
        });

        if (periodPaginationSummary) {
            periodPaginationSummary.textContent = `Menampilkan ${firstItem + 1} hingga ${lastItem} dari ${periodRows.length} periode.`;
        }
        if (periodPagination) {
            periodPagination.innerHTML = paginationMarkup(safePage, totalPages, 'data-period-page');
        }
    };

    periodPagination?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-period-page]');
        if (button && !button.disabled) renderPeriodPage(Number(button.dataset.periodPage));
    });
    if (periodPagination && periodRows.length) renderPeriodPage();

    document.querySelectorAll('[data-view-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            const mode = button.dataset.viewMode;
            const panel = button.closest('.satisfaction-panel');

            button.parentElement.querySelectorAll('button').forEach((item) => item.classList.toggle('active', item === button));
            panel.classList.toggle('chart-mode', mode === 'chart');

            if (mode === 'chart' && !kepuasanStackChart && kategoriDetails.length) {
                kepuasanStackChart = new ApexCharts(document.querySelector('#chart-kepuasan-stack'), kepuasanStackOptions());
                kepuasanStackChart.render();
            }
        });
    });
</script>
@endsection
