@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-neutral-900">
                Manajemen User
            </h2>
            <p class="text-sm text-neutral-500">
                Kelola pengguna dan role sistem.
            </p>
        </div>

        <a
            href="{{ route('users.create') }}"
            class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
        >
            Tambah User
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-6 py-3">Nama</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">NIP</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100 bg-white">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-6 py-4 font-medium text-neutral-900">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $user->nip ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $user->roles->pluck('name')->implode(', ') }}
                            </td>

                            <td class="px-6 py-4">
                                @if ($user->is_active)
                                    <span class="rounded-full bg-success-background px-2.5 py-1 text-xs font-medium text-success-text">
                                        Aktif
                                    </span>
                                @else
                                    <span class="rounded-full bg-danger-background px-2.5 py-1 text-xs font-medium text-danger-text">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a
                                        href="{{ route('users.edit', $user) }}"
                                        class="text-xs font-semibold text-primary-base hover:text-primary-hover"
                                    >
                                        Edit
                                    </a>

                                    @if (! auth()->user()->is($user))
                                        <form
                                            method="POST"
                                            action="{{ route('users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus user ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-xs font-semibold text-danger-base hover:text-danger-text"
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
                            <td colspan="6" class="px-6 py-8 text-center text-neutral-500">
                                Belum ada user.
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
@endsection