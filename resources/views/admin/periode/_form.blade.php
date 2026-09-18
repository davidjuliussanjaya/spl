<div class="mb-3">
    <label for="kode_periode" class="form-label fw-bold">Kode periode <span class="text-danger">*</span></label>
    <input id="kode_periode" name="kode_periode" type="text" maxlength="50" class="form-control @error('kode_periode') is-invalid @enderror" value="{{ old('kode_periode', $periode->kode_periode ?? '') }}" placeholder="Contoh: TS-2026-GENAP" required>
    @error('kode_periode')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="nama_periode" class="form-label fw-bold">Nama periode <span class="text-danger">*</span></label>
    <input id="nama_periode" name="nama_periode" type="text" class="form-control @error('nama_periode') is-invalid @enderror" value="{{ old('nama_periode', $periode->nama_periode ?? '') }}" placeholder="Contoh: Tracer Study 2026 Gelombang 1" required>
    @error('nama_periode')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3"><label for="tanggal_mulai" class="form-label fw-bold">Tanggal mulai <span class="text-danger">*</span></label><input id="tanggal_mulai" name="tanggal_mulai" type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai', isset($periode) ? $periode->tanggal_mulai->format('Y-m-d') : '') }}" required>@error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6 mb-3"><label for="tanggal_berakhir" class="form-label fw-bold">Tanggal berakhir <span class="text-danger">*</span></label><input id="tanggal_berakhir" name="tanggal_berakhir" type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" value="{{ old('tanggal_berakhir', isset($periode) ? $periode->tanggal_berakhir->format('Y-m-d') : '') }}" required>@error('tanggal_berakhir')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div>
