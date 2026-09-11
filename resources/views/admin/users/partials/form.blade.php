@php
    $isEdit = isset($user);
    $selectedRole = old('role', $isEdit ? $user->roles->first()?->code : 'user');
    $isActive = (bool) old('is_active', $isEdit ? $user->is_active : true);
@endphp

@if($errors->any())
    <div class="alert alert-danger spl-alert" role="alert"><i class="bi bi-exclamation-circle-fill me-2"></i>Periksa kembali isian yang ditandai.</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <section class="card">
            <div class="card-body p-4">
                <h5 class="mb-4">Informasi akun</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Alamat email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Kata sandi {{ $isEdit ? '(opsional)' : '' }}</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ $isEdit ? '' : 'required' }} autocomplete="new-password">
                        @if($isEdit)<div class="form-text">Kosongkan bila kata sandi tidak perlu diubah.</div>@endif
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label">Konfirmasi kata sandi</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" {{ $isEdit ? '' : 'required' }} autocomplete="new-password">
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="col-lg-4">
        <section class="card">
            <div class="card-body p-4">
                <h5 class="mb-4">Hak akses</h5>
                <div class="mb-4">
                    <label for="role" class="form-label">Peran</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" {{ $isEdit && $user->is(auth()->user()) ? 'disabled' : '' }} required>
                        <option value="admin" @selected($selectedRole === 'admin')>Administrator</option>
                        <option value="user" @selected($selectedRole === 'user')>Pengguna biasa</option>
                    </select>
                    @if($isEdit && $user->is(auth()->user()))<input type="hidden" name="role" value="admin"><div class="form-text">Peran akun sendiri tidak dapat diubah.</div>@endif
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-check form-switch mb-4">
                    @if($isEdit && $user->is(auth()->user()))
                        <input type="hidden" name="is_active" value="1">
                    @else
                        <input type="hidden" name="is_active" value="0">
                    @endif
                    <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input" @checked($isActive) {{ $isEdit && $user->is(auth()->user()) ? 'disabled' : '' }}>
                    <label for="is_active" class="form-check-label">Akun aktif</label>
                    @if($isEdit && $user->is(auth()->user()))<div class="form-text">Akun sendiri tidak dapat dinonaktifkan.</div>@endif
                </div>
                @error('is_active')<div class="text-danger small mb-3">{{ $message }}</div>@enderror
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> {{ $submitLabel }}</button>
            </div>
        </section>
    </div>
</div>
