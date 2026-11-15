{{-- Dipakai bersama oleh create.blade.php dan edit.blade.php --}}
@php($editing = isset($user))

<div class="form-group">
    <label for="name">Nama</label>
    <input type="text" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
    @error('name') <small class="field-error">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
    @error('email') <small class="field-error">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label for="role">Peran</label>
    <select id="role" name="role" required>
        @foreach(['user' => 'User (pelanggan)', 'admin' => 'Admin'] as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $user->role ?? 'user') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('role') <small class="field-error">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label for="password">{{ $editing ? 'Password baru (kosongkan jika tidak diganti)' : 'Password' }}</label>
    <input type="password" id="password" name="password" minlength="6" {{ $editing ? '' : 'required' }}>
    @error('password') <small class="field-error">{{ $message }}</small> @enderror
</div>

<div class="form-group">
    <label for="password_confirmation">Konfirmasi password</label>
    <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" {{ $editing ? '' : 'required' }}>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan Pengguna' }}</button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
</div>