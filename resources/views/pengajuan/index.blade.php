@extends('layouts.admin')

@section('title', 'Pengajuan Surat')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-neutral-900">
                Daftar Pengajuan Surat
            </h2>
            <p class="text-sm text-neutral-500">
                Kelola pengajuan surat sesuai role dan status.
            </p>
        </div>

        @if (auth()->user()->hasAnyRole(['Admin Fakultas', 'Super Admin']))
            <a
                href="{{ route('pengajuan.create') }}"
                class="inline-flex items-center justify-center rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
            >
                Buat Pengajuan Baru
            </a>
        @endif
    </div>

    <div class="mb-4 rounded-lg border border-neutral-200 bg-white p-4 shadow-sm">
        <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                >
                    <option value="">Semua Status</option>

                    @foreach (\App\Enums\StatusPengajuan::cases() as $status)
                        <option
                            value="{{ $status->value }}"
                            @selected(request('status') === $status->value)
                        >
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                >
                    Filter
                </button>

                <a
                    href="{{ route('pengajuan.index') }}"
                    class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-6 py-3">No Tiket</th>
                        <th class="px-6 py-3">Jenis Surat</th>
                        <th class="px-6 py-3">Pemohon</th>
                        <th class="px-6 py-3">Nomor Surat</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100 bg-white">
                    @forelse ($pengajuanList as $item)
                        <tr>
                            <td class="px-6 py-4 font-medium text-neutral-900">
                                {{ $item->no_tiket }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->jenisSurat?->nama }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->pemohon?->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->nomor_surat ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                @include('partials.status-badge', ['status' => $item->status])
                            </td>

                            <td class="px-6 py-4 text-neutral-600">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>

                           <td class="px-6 py-4 text-right">
    <div class="flex items-center justify-end gap-2">
        <a
            href="{{ route('pengajuan.show', $item) }}"
            class="text-xs font-semibold text-primary-base hover:text-primary-hover"
        >
            Lihat
        </a>

        @can('update', $item)
            <a
                href="{{ route('pengajuan.edit', $item) }}"
                class="text-xs font-semibold text-warning-base hover:text-warning-hover"
            >
                Edit
            </a>
        @endcan

        @can('submit', $item)
            <form method="POST" action="{{ route('pengajuan.submit', $item) }}" class="inline">
                @csrf
                <button
                    type="submit"
                    onclick="return confirm('Ajukan surat ini ke BAAK?')"
                    class="text-xs font-semibold text-success-base hover:text-success-hover"
                >
                    Ajukan
                </button>
            </form>
        @endcan

        @can('delete', $item)
            <form method="POST" action="{{ route('pengajuan.destroy', $item) }}" class="inline">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    onclick="return confirm('Hapus draft ini?')"
                    class="text-xs font-semibold text-danger-base hover:text-danger-hover"
                >
                    Hapus
                </button>
            </form>
        @endcan
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-neutral-500">
                                Belum ada data pengajuan surat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengajuanList->hasPages())
            <div class="border-t border-neutral-200 px-6 py-4">
                {{ $pengajuanList->links() }}
            </div>
        @endif
    </div>
@endsection