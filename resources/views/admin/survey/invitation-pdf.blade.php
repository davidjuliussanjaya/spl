<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px 42px; }
        body { color: #172554; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.55; }
        .header { border-bottom: 3px solid #2563eb; padding: 0 0 13px; }
        .logo { height: 46px; vertical-align: middle; }
        .brand { display: inline-block; margin-left: 11px; vertical-align: middle; }
        .brand strong { display: block; color: #0f2d68; font-size: 14px; letter-spacing: .2px; }
        .brand span { color: #64748b; font-size: 8px; }
        .eyebrow { color: #2563eb; font-size: 8px; font-weight: bold; letter-spacing: 1.1px; margin: 25px 0 4px; text-transform: uppercase; }
        h1 { color: #102a60; font-size: 21px; line-height: 1.18; margin: 0 0 6px; }
        .intro { color: #64748b; margin: 0; }
        .salutation { margin: 18px 0 13px; }
        .details { background: #f8fbff; border: 1px solid #dbeafe; border-radius: 9px; padding: 11px 16px; }
        table { border-collapse: collapse; width: 100%; }
        td { border-bottom: 1px solid #e5eefb; padding: 7px 0; vertical-align: top; }
        tr:last-child td { border-bottom: 0; }
        td:first-child { color: #64748b; width: 36%; }
        .access { margin-top: 18px; }
        .access-title { color: #102a60; font-size: 11px; font-weight: bold; margin: 0 0 7px; }
        .code-box { background: #1d4ed8; border-radius: 7px; color: #fff; display: inline-block; font-size: 16px; font-weight: bold; letter-spacing: 2px; padding: 8px 13px; }
        .instruction { background: #eff6ff; border-left: 4px solid #2563eb; color: #334155; margin-top: 12px; padding: 10px 13px; }
        .url { color: #1d4ed8; font-size: 9px; overflow-wrap: anywhere; }
        .foot { border-top: 1px solid #cbd5e1; color: #64748b; font-size: 8px; margin-top: 25px; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img class="logo" src="{{ public_path('assets/images/logo/undika.png') }}" alt="Universitas Dinamika">
        <div class="brand">
            <strong>UNIVERSITAS DINAMIKA</strong>
            <span>Sistem Pelacakan Lulusan - Evaluasi Pengguna Lulusan</span>
        </div>
    </div>

    <p class="eyebrow">Undangan pengisian survei</p>
    <h1>Mohon partisipasi Anda</h1>
    <p class="intro">Kami mengundang perusahaan untuk memberikan penilaian terhadap lulusan Universitas Dinamika.</p>

    <p class="salutation">Yth. Bapak/Ibu <b>{{ $survey->penggunalulusan->nama_penyelia ?? 'Responden' }}</b>,<br>
        Masukan Anda sangat berarti untuk peningkatan kualitas lulusan kami.</p>

    <div class="details">
        <table>
            <tr><td>Nama perusahaan</td><td><b>{{ $survey->penggunalulusan->nama_perusahaan ?? '-' }}</b></td></tr>
            <tr><td>Nama responden</td><td><b>{{ $survey->penggunalulusan->nama_penyelia ?? '-' }}</b></td></tr>
            <tr><td>Lulusan yang dinilai</td><td><b>{{ $survey->lulusan->nama ?? '-' }}</b></td></tr>
        </table>
    </div>

    <div class="access">
        <p class="access-title">Kode akses survei</p>
        <span class="code-box">{{ $survey->access_code }}</span>
        <div class="instruction"><b>Buka halaman pengisian:</b> <span class="url">{{ $fillUrl }}</span><br>
            Masukkan kode akses di atas secara manual, lengkapi pertanyaan, lalu kirim jawaban.</div>
    </div>

    <div class="foot">Dokumen ini dibuat otomatis oleh Sistem Pelacakan Lulusan Universitas Dinamika. Terima kasih atas waktu dan partisipasi Anda.</div>
</body>
</html>
