@extends('layouts.admin')

@section('title', 'Nomor & Verifikasi')

@section('content')
    <div>
        {{-- Header --}}
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-neutral-900">
                Antrian Nomor & Verifikasi
            </h2>
            <p class="text-sm text-neutral-500">
                Periksa surat melalui tombol Lihat Surat, lalu tetapkan nomor dan verifikasi dari halaman detail.
            </p>
        </div>

        {{-- Info Status Auto Number --}}
        @if (! config('surat.auto_number_enabled'))
            <div class="mb-4 flex items-start gap-3 rounded-md border border-warning-border bg-warning-background px-4 py-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 flex-shrink-0 text-warning-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-warning-text">
                        Nomor Otomatis Dinonaktifkan
                    </p>
                    <p class="mt-1 text-xs text-warning-text">
                        Semua nomor surat wajib diisi manual oleh BAAK melalui halaman detail pengajuan.
                    </p>
                </div>
            </div>
        @endif

        {{-- Panduan Alur Kerja --}}
        <div class="mb-4 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="rounded-md border border-neutral-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-primary-base">Langkah 1</p>
                <p class="mt-1 text-sm font-medium text-neutral-900">Lihat Surat</p>
                <p class="mt-1 text-xs text-neutral-500">
                    Periksa data pengajuan, form dinamis, dan lampiran pendukung.
                </p>
            </div>
            <div class="rounded-md border border-neutral-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-primary-base">Langkah 2</p>
                <p class="mt-1 text-sm font-medium text-neutral-900">Tetapkan Nomor</p>
                <p class="mt-1 text-xs text-neutral-500">
                    Isi atau perbaiki nomor surat pada form di kartu Aksi Proses.
                </p>
            </div>
            <div class="rounded-md border border-neutral-200 bg-white px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-primary-base">Langkah 3</p>
                <p class="mt-1 text-sm font-medium text-neutral-900">Verifikasi</p>
                <p class="mt-1 text-xs text-neutral-500">
                    Approve untuk kirim ke penandatangan, atau tolak jika data cacat.
                </p>
            </div>
        </div>

        {{-- Tabel Antrian --}}
        <div class="overflow-hidden rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-200 text-sm">
                    <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                        <tr>
                            <th class="px-6 py-3">No Tiket</th>
                            <th class="px-6 py-3">Jenis Surat</th>
                            <th class="px-6 py-3">Pemohon</th>
                            <th class="px-6 py-3">Penandatangan</th>
                            <th class="px-6 py-3">Nomor Surat</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-neutral-100 bg-white">
                        @forelse ($antrian as $item)
                            <tr class="hover:bg-neutral-50">
                                <td class="px-6 py-4 font-medium text-neutral-900">
                                    {{ $item->no_tiket }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $item->jenisSurat?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $item->pemohon?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if ($item->targetSigner)
                                        <span class="font-medium text-neutral-900">{{ $item->targetSigner->name }}</span>
                                        <span class="block text-neutral-500">
                                            NIP: {{ $item->targetSigner->nip ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ $item->nomor_surat ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    @include('partials.status-badge', ['status' => $item->status])
                                </td>
                                <td class="px-6 py-4 text-right">
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
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-neutral-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mb-3 h-12 w-12 text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm font-medium">Tidak ada antrian</p>
                                        <p class="mt-1 text-xs">
                                            Belum ada pengajuan yang menunggu nomor atau verifikasi.
                                        </p>
                                    </div>
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
    </div>
@endsection