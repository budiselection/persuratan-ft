@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <div>
        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900">
                    Manajemen User
                </h2>
                <p class="text-sm text-neutral-500">
                    Kelola pengguna sistem dan role mereka.
                </p>
            </div>

            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center gap-1.5 rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-primary-text hover:bg-primary-hover"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-md border border-success-border bg-success-background px-4 py-3 text-sm text-success-text">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel User --}}
        <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 text-sm">
                    <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                        <tr>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">NIP/NIM</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-neutral-100 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-6 py-4 font-medium text-neutral-900">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-neutral-600">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    @foreach ($user->roles as $role)
                                        <span class="inline-block rounded bg-primary-extra-light px-2 py-1 text-xs font-semibold text-primary-base">
                                            {{ $role->name }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-neutral-600">
                                    {{ $user->nip ?? $user->nim ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($user->is_active)
                                        <span class="inline-block rounded bg-success-background px-2 py-1 text-xs font-semibold text-success-text">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-block rounded bg-neutral-100 px-2 py-1 text-xs font-semibold text-neutral-600">
                                            Nonaktif
                                        </span>
                                    @endif

                                    @if ($user->email_verified_at === null && $user->password !== null)
                                        <span class="ml-1 inline-block rounded bg-warning-background px-2 py-1 text-xs font-semibold text-warning-text">
                                            Belum Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- ★ BADGE MENUNGGU AKTIVASI (untuk user pending, password null) ★ --}}
                                        @if ($user->password === null)
                                            <span class="rounded bg-warning-background px-2 py-1 text-xs font-semibold text-warning-text">
                                                Menunggu Aktivasi
                                            </span>
                                            <form method="POST" action="{{ route('users.resend-otp', $user) }}" class="inline">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="text-xs font-semibold text-primary-base underline hover:text-primary-hover"
                                                >
                                                    Kirim Ulang OTP
                                                </button>
                                            </form>
                                        @endif

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            class="text-xs font-semibold text-primary-base hover:text-primary-hover"
                                        >
                                            Edit
                                        </a>

                                        {{-- Cegah penghapusan diri sendiri dan Super Admin terakhir --}}
                                        @if ($user->id !== auth()->id())
                                            <form
                                                method="POST"
                                                action="{{ route('users.destroy', $user) }}"
                                                onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')"
                                                class="inline"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-xs font-semibold text-danger-base hover:text-danger-hover"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-neutral-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada user</p>
                                        <p class="mt-1 text-xs">Klik "Tambah User" untuk membuat user pertama.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-neutral-200 px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection