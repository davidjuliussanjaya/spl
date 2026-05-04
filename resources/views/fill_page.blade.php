<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $survey->judul }} - Survey Kepuasan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        /* Container & Cards */
        .survey-container {
            max-width: 850px;
            margin: 0 auto;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        /* Header Survey */
        .survey-header {
            background: linear-gradient(135deg, #435ebe 0%, #384ea1 100%);
            color: #fff;
            padding: 3rem 2rem;
            text-align: center;
        }
        .survey-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        /* Info Box */
        .info-box {
            background-color: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 1rem;
        }

        /* Kategori Title */
        .kategori-title {
            font-weight: 700;
            color: #1e293b;
            background-color: #f1f5f9;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            border-left: 5px solid #435ebe;
        }

        /* Form Elements */
        .form-control {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
        }

        /* Custom Radio Options (Grid Layout) */
        .option-card {
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            background-color: #fff;
        }
        .option-card:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
        }
        .btn-check:checked + .option-card {
            border-color: #435ebe;
            background-color: #eff6ff;
        }
        .btn-check:checked + .option-card .radio-circle {
            border-color: #435ebe;
            background-color: #435ebe;
            box-shadow: inset 0 0 0 3px #eff6ff;
        }
        .radio-circle {
            width: 22px;
            height: 22px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-bottom: 10px;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .option-text {
            font-size: 0.9rem;
            font-weight: 500;
            color: #334155;
        }

        /* Button */
        .btn-submit {
            background-color: #435ebe;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background-color: #384ea1;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 94, 190, 0.2);
        }

        .border-bottom-dashed { border-bottom: 2px dashed #e2e8f0; }
    </style>
</head>
<body>

    <div class="container py-5 survey-container">
        
        @if(session('error'))
            <div class="alert alert-danger shadow-sm rounded-3 border-0 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card-custom">
            <div class="survey-header">
                <h2>{{ $survey->judul }}</h2>
                <p class="text-white-50 mb-0">Kode Akses Sesi: <span class="fw-bold text-white tracking-widest">{{ $survey->access_code }}</span></p>
            </div>

            <div class="p-4 p-md-5">
                @if($survey->deskripsi)
                    <div class="alert alert-light border mb-4 text-secondary" style="line-height: 1.6;">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i> {{ $survey->deskripsi }}
                    </div>
                @endif

                <h5 class="fw-bold mb-4 text-dark">A. Informasi tentang Institusi / Perusahaan dan Responden</h5>

                <div class="info-box row g-0">
                    <div class="col-md-6 pe-md-4 border-md-end">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Identitas Perusahaan</h6>
                        </div>
                        <div class="info-label">Nama Perusahaan</div>
                        <div class="info-value">{{ $survey->penggunalulusan->nama_perusahaan ?? '-' }}</div>
                        
                        <div class="info-label">Badan Hukum & Kontak</div>
                        <div class="info-value">
                            {{ $survey->penggunalulusan->nomor_badan_hukum ?? '-' }} <br>
                            <span class="text-muted fw-normal">{{ $survey->penggunalulusan->kontak_perusahaan ?? '-' }}</span>
                        </div>

                        <div class="info-label">Alamat</div>
                        <div class="info-value mb-md-0">{{ $survey->penggunalulusan->alamat_perusahaan ?? '-' }}</div>
                    </div>
                    
                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-2 rounded me-3">
                                <i class="bi bi-mortarboard fs-5"></i>
                            </div>
                            <h6 class="mb-0 fw-bold">Identitas Lulusan Terkait</h6>
                        </div>
                        <div class="info-label">Nama Lulusan</div>
                        <div class="info-value">{{ $survey->lulusan->nama ?? '-' }}</div>
                        
                        <div class="info-label">NIM & Program Studi</div>
                        <div class="info-value mb-0">
                            {{ $survey->lulusan->nim ?? '-' }} <br>
                            <span class="text-muted fw-normal">{{ $survey->lulusan->program_studi ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('survey.submit', $survey->access_code) }}" method="POST">
            @csrf
            
            <div class="card-custom p-4 p-md-5">
                <div class="mb-2">
                    <label class="form-label fs-5 fw-bold text-dark">Konfirmasi Identitas Responden <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-3">Mohon konfirmasi atau lengkapi data Anda sebagai perwakilan instansi yang mengisi kuesioner ini.</p>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label small text-secondary fw-bold mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_pengisi" class="form-control" required placeholder="Nama Anda..." value="{{ $survey->penggunalulusan->nama_penyelia ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Nomor HP</label>
                        <input type="text" name="hp_pengisi" class="form-control" placeholder="08..." value="{{ $survey->penggunalulusan->kontak_penyelia ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-secondary fw-bold mb-1">Alamat Email</label>
                        <input type="email" name="email_pengisi" class="form-control" placeholder="email@instansi.com" value="{{ $survey->penggunalulusan->email_penyelia ?? '' }}">
                    </div>
                </div>
            </div>

            @php
                // Mengelompokkan soal berdasarkan field 'kategori'
                $groupedSoal = $soal->groupBy('kategori');
            @endphp

            @foreach($groupedSoal as $kategori => $soalGroup)
                <div class="card-custom p-4 p-md-5">
                    
                    <div class="kategori-title mb-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $kategori }}</h5>
                    </div>

                    @foreach($soalGroup as $index => $s)
                        <div class="mb-4 {{ !$loop->last ? 'pb-4 border-bottom-dashed' : '' }}">
                            <label class="form-label fs-6 mb-3 text-dark fw-bold">
                                <span class="text-primary me-1">{{ $index + 1 }}.</span> {{ $s->soal }}
                                @if($s->is_required) <span class="text-danger">*</span> @endif
                            </label>

                            <div class="ps-3 ps-md-4">
                                @if($s->jenis_soal == 'essay')
                                    <textarea name="jawaban[{{ $s->id }}]" class="form-control" rows="3" 
                                              placeholder="Tuliskan umpan balik Anda di sini..." 
                                              {{ $s->is_required ? 'required' : '' }}></textarea>
                                @else
                                    <div class="row g-2">
                                        @foreach($s->jawaban as $j)
                                            <div class="col-6 col-md-3">
                                                <input class="btn-check" type="radio" 
                                                       name="jawaban[{{ $s->id }}]" 
                                                       id="opt_{{ $s->id }}_{{ $j->id }}" 
                                                       value="{{ $j->id }}" {{ $s->is_required ? 'required' : '' }}>
                                                
                                                <label class="option-card w-100 m-0" for="opt_{{ $s->id }}_{{ $j->id }}">
                                                    <div class="radio-circle"></div>
                                                    <span class="option-text">{{ $j->jawaban }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>
            @endforeach

            <div class="mt-4 mb-5">
                <button type="submit" class="btn-submit">
                    Kirim Kuesioner Evaluasi <i class="bi bi-send-check ms-2"></i>
                </button>
                <p class="text-center text-muted small mt-3">
                    <i class="bi bi-shield-lock-fill text-success"></i> Data umpan balik ini disimpan dengan aman dan rahasia.
                </p>
            </div>

        </form>
    </div>

</body>
</html>