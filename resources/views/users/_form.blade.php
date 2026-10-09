@php
    $user = $user ?? null;
    $roles = ['Super Admin', 'Admin Fakultas', 'BAAK', 'Penandatangan', 'Dosen', 'Mahasiswa'];
@endphp

<div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        {{-- Nama --}}
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-neutral-700">
                Nama Lengkap <span class="text-danger-base">*</span>
            </label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user?->name) }}"
                required
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
            @error('name')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-neutral-700">
                Email <span class="text-danger-base">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user?->email) }}"
                required
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
            <p class="mt-1 text-xs text-neutral-500">
                Wajib berakhiran @{{ config('surat.email_domain') }}
            </p>
            @error('email')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- Role --}}
        <div>
            <label for="role" class="block text-sm font-medium text-neutral-700">
                Role <span class="text-danger-base">*</span>
            </label>
            <select
                id="role"
                name="role"
                required
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
                @foreach ($roles as $role)
                    <option
                        value="{{ $role }}"
                        @selected(old('role', $user?->roles->first()?->name) === $role)
                    >
                        {{ $role }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- NIP --}}
        <div>
            <label for="nip" class="block text-sm font-medium text-neutral-700">
                NIP / NUPTK (Staff/Dosen)
            </label>
            <input
                type="text"
                id="nip"
                name="nip"
                value="{{ old('nip', $user?->nip) }}"
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
            @error('nip')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- NIM --}}
        <div>
            <label for="nim" class="block text-sm font-medium text-neutral-700">
                NIM (Mahasiswa)
            </label>
            <input
                type="text"
                id="nim"
                name="nim"
                value="{{ old('nim', $user?->nim) }}"
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
            @error('nim')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-neutral-700">
                Password
            </label>
            <input
                type="password"
                id="password"
                name="password"
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
            <p class="mt-1 text-xs text-neutral-500">
                Kosongkan untuk mengirim OTP aktivasi ke email user (disarankan untuk Dosen).
            </p>
            @error('password')
                <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-neutral-700">
                Konfirmasi Password
            </label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
        </div>

        {{-- Status Aktif --}}
        <div class="md:col-span-2">
            <label class="flex items-center gap-2 text-sm text-neutral-700">
                <input
                    type="hidden"
                    name="is_active"
                    value="0"
                >
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    @checked(old('is_active', $user?->is_active ?? true))
                    class="rounded border-neutral-300 text-primary-base focus:ring-primary-focus"
                >
                Akun Aktif
            </label>
        </div>
    </div>
</div>