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

        /* Card & Containers */
        .survey-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
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
        }

        /* Info Box */
        .info-box {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #edf2f7;
        }
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-weight: 500;
            color: #2b3445;
            margin-bottom: 1rem;
        }

        /* Form Elements */
        .form-label {
            font-weight: 600;
            color: #2b3445;
        }
        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #435ebe;
            box-shadow: 0 0 0 4px rgba(67, 94, 190, 0.1);
        }

        /* Custom Radio Options */
        .option-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            height: 100%;
        }
        .option-card:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }
        .btn-check:checked + .option-card {
            border-color: #435ebe;
            background-color: rgba(67, 94, 190, 0.05);
        }
        .btn-check:checked + .option-card .radio-circle {
            border-color: #435ebe;
            background-color: #435ebe;
            box-shadow: inset 0 0 0 3px #fff;
        }
        .radio-circle {
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            margin-right: 12px;
            flex-shrink: 0;
            transition: all 0.2s;
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
                <p class="text-white-50 mb-0">Kode Sesi: <span class="fw-bold text-white">{{ $survey->access_code }}</span></p>
            </div>

            <div class="p-4 p-md-5">
                @if($survey->deskripsi)
                    <div class="mb-4 text-secondary" style="line-height: 1.6;">
                        {{ $survey->deskripsi }}
                    </div>
                @endif

                <div class="info-box row g-0">
                    <div class="col-md-6 pe-md-4 border-md-end">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-building fs-4 text-primary me-2"></i>
                            <h6 class="mb-0 fw-bold">Data Instansi</h6>
                        </div>
                        <div class="info-label">Nama Perusahaan</div>
                        <div class="info-value">{{ $survey->pengguna_lulusan->nama_perusahaan ?? '-' }}</div>
                        
                        <div class="info-label">Penyelia / Kontak</div>
                        <div class="info-value mb-md-0">{{ $survey->pengguna_lulusan->nama_penyelia ?? '-' }}</div>
                    </div>
                    
                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-mortarboard fs-4 text-primary me-2"></i>
                            <h6 class="mb-0 fw-bold">Data Lulusan Terkait</h6>
                        </div>
                        <div class="info-label">Nama Alumni</div>
                        <div class="info-value">{{ $survey->lulusan->nama ?? '-' }}</div>
                        
                        <div class="info-label">NIM / Program Studi</div>
                        <div class="info-value mb-0">
                            {{ $survey->lulusan->nim ?? '-' }} <br>
                            <span class="text-muted small">{{ $survey->lulusan->program_studi ?? '' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-custom p-4 p-md-5">
            <form action="{{ route('survey.submit', $survey->access_code) }}" method="POST">
                @csrf
                
                <div class="mb-5 pb-4 border-bottom">
                    <label class="form-label fs-5">Konfirmasi Nama Responden <span class="text-danger">*</span></label>
                    <p class="text-muted small mb-3">Silakan sesuaikan nama Anda jika berbeda dengan data penyelia di atas.</p>
                    <input type="text" name="nama_pengisi" class="form-control form-control-lg" required 
                           placeholder="Masukkan nama lengkap Anda..." 
                           value="{{ $survey->pengguna_lulusan->nama_penyelia ?? '' }}">
                </div>

                @foreach($soal as $index => $s)
                    <div class="mb-5">
                        <label class="form-label fs-6 mb-3">
                            <span class="text-primary fw-bold me-1">{{ $index + 1 }}.</span> {{ $s->soal }}
                            @if($s->is_required) <span class="text-danger">*</span> @endif
                        </label>

                        <div class="ps-3 ps-md-4">
                            @if($s->jenis_soal == 'essay')
                                <textarea name="jawaban[{{ $s->id }}]" class="form-control" rows="4" 
                                          placeholder="Tuliskan jawaban Anda di sini..." 
                                          {{ $s->is_required ? 'required' : '' }}></textarea>
                            @else
                                <div class="row g-3">
                                    @foreach($s->jawaban as $j)
                                        <div class="col-md-6">
                                            <input class="btn-check" type="radio" 
                                                   name="jawaban[{{ $s->id }}]" 
                                                   id="opt_{{ $s->id }}_{{ $j->id }}" 
                                                   value="{{ $j->id }}" {{ $s->is_required ? 'required' : '' }}>
                                            
                                            <label class="option-card w-100 m-0" for="opt_{{ $s->id }}_{{ $j->id }}">
                                                <div class="radio-circle"></div>
                                                <span class="text-dark">{{ $j->jawaban }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-5 pt-3">
                    <button type="submit" class="btn-submit">
                        Kirim Jawaban Survey <i class="bi bi-send ms-2"></i>
                    </button>
                    <p class="text-center text-muted small mt-3">
                        <i class="bi bi-lock-fill"></i> Data yang Anda kirimkan dijamin kerahasiaannya.
                    </p>
                </div>
            </form>
        </div>
        
    </div>

</body>
</html>