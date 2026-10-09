@extends('layouts.admin')

@section('title', 'Antrian Tanda Tangan')

@section('content')
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-neutral-900">
            Antrian Tanda Tangan
        </h2>
        <p class="text-sm text-neutral-500">
            Daftar surat yang menunggu tanda tangan digital.
        </p>
    </div>

    @if (! auth()->user()->signature_path)
        <div class="mb-4 rounded-md border border-warning-border bg-warning-background px-4 py-3 text-sm text-warning-text">
            Anda belum mengunggah tanda tangan digital.

            <a
                href="{{ route('signature.create') }}"
                class="font-semibold underline"
            >
                Unggah sekarang
            </a>
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-6 py-3">No Tiket</th>
                        <th class="px-6 py-3">Nomor Surat</th>
                        <th class="px-6 py-3">Jenis Surat</th>
                        <th class="px-6 py-3">Pemohon</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100 bg-white">
                    @forelse ($antrian as $item)
                        <tr>
                            <td class="px-6 py-4 font-medium text-neutral-900">
                                {{ $item->no_tiket }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->nomor_surat ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->jenisSurat?->nama }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->pemohon?->name }}
                            </td>

                           <td class="px-6 py-4 text-right">
    <div class="flex items-center justify-end gap-2">
        @can('view', $item)
            <a
                href="{{ route('pengajuan.show', $item) }}"
                class="inline-flex items-center gap-1.5 rounded-md border border-primary-border bg-primary-extra-light px-3 py-1.5 text-xs font-semibold text-primary-base hover:bg-primary-light"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Lihat Surat
            </a>
        @endcan

        @can('sign', $item)
            <a
                href="{{ route('ttd.sign-form', $item) }}"
                class="inline-flex items-center gap-1.5 rounded-md bg-secondary-base px-3 py-1.5 text-xs font-semibold text-secondary-text hover:bg-secondary-hover"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Proses TTD
            </a>
        @endcan
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-neutral-500">
                                Tidak ada surat yang menunggu tanda tangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($antrian->hasPages())
            <div class="border-t border-neutral-200 px-6 py-4">
                {{ $antrian->links() }}
            </div>
        @endif
    </div>
@endsection