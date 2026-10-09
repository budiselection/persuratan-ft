@extends('layouts.admin')

@section('title', 'Proses Tanda Tangan')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-neutral-900">
                Proses Tanda Tangan
            </h2>
            <p class="text-sm text-neutral-500">
                No Tiket: {{ $pengajuan->no_tiket }}
            </p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
            <form
                method="POST"
                action="{{ route('ttd.sign', $pengajuan) }}"
                class="space-y-6"
            >
                @csrf

                {{-- Info Pengajuan --}}
                <div class="rounded-md border border-neutral-200 bg-neutral-50 p-4">
                    <h3 class="text-sm font-semibold text-neutral-900">Informasi Surat</h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Jenis Surat</dt>
                            <dd class="font-medium text-neutral-900">{{ $pengajuan->jenisSurat?->nama }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Pemohon</dt>
                            <dd class="font-medium text-neutral-900">{{ $pengajuan->pemohon?->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-neutral-500">Nomor Surat</dt>
                            <dd class="font-medium text-neutral-900">{{ $pengajuan->nomor_surat ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Preview PDF --}}
                <div>
                    <a
                        href="{{ route('ttd.preview', $pengajuan) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-md border border-primary-border bg-primary-extra-light px-4 py-2 text-sm font-semibold text-primary-base hover:bg-primary-light"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview PDF
                    </a>
                </div>

                {{-- Pilihan Mode --}}
                <div>
                    <label class="block text-sm font-medium text-neutral-700">
                        Mode Tanda Tangan <span class="text-danger-base">*</span>
                    </label>

                    <div class="mt-3 space-y-3">
                        {{-- Mode Digital --}}
                        <label class="flex items-start gap-3 rounded-md border border-neutral-200 p-4 cursor-pointer hover:bg-neutral-50">
                            <input
                                type="radio"
                                name="mode_ttd"
                                value="digital"
                                {{ old('mode_ttd', 'digital') === 'digital' ? 'checked' : '' }}
                                {{ ! $hasSignature ? 'disabled' : '' }}
                                class="mt-1 text-primary-base focus:ring-primary-focus"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-neutral-900">
                                    Tanda Tangan Digital
                                </span>
                                <p class="mt-1 text-xs text-neutral-500">
                                    PDF akan menampilkan gambar tanda tangan Anda + QR code verifikasi.
                                </p>
                                @if (! $hasSignature)
                                    <p class="mt-2 text-xs text-danger-base">
                                        ⚠️ Anda belum mengunggah tanda tangan digital.
                                        <a href="{{ route('signature.create') }}" class="underline" target="_blank">
                                            Upload sekarang
                                        </a>
                                    </p>
                                @endif
                            </div>
                        </label>

                        {{-- Mode Manual --}}
                        <label class="flex items-start gap-3 rounded-md border border-neutral-200 p-4 cursor-pointer hover:bg-neutral-50">
                            <input
                                type="radio"
                                name="mode_ttd"
                                value="manual"
                                {{ old('mode_ttd') === 'manual' ? 'checked' : '' }}
                                class="mt-1 text-primary-base focus:ring-primary-focus"
                            >
                            <div class="flex-1">
                                <span class="text-sm font-medium text-neutral-900">
                                    Konfirmasi Manual (Tanpa Tanda Tangan)
                                </span>
                                <p class="mt-1 text-xs text-neutral-500">
                                    PDF hanya menampilkan QR code verifikasi. Tanda tangan basah dilakukan setelah dicetak.
                                </p>
                            </div>
                        </label>
                    </div>

                    @error('mode_ttd')
                        <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal TTD --}}
                <div>
                    <label for="tanggal_ttd" class="block text-sm font-medium text-neutral-700">
                        Tanggal Tanda Tangan <span class="text-danger-base">*</span>
                    </label>

                    <input
                        type="date"
                        id="tanggal_ttd"
                        name="tanggal_ttd"
                        value="{{ old('tanggal_ttd', now()->format('Y-m-d')) }}"
                        max="{{ now()->format('Y-m-d') }}"
                        required
                        class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                    >

                    <p class="mt-1 text-xs text-neutral-500">
                        Default: hari ini. Bisa diubah sesuai kebutuhan (maksimal hari ini).
                    </p>

                    @error('tanggal_ttd')
                        <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end gap-3 pt-4">
                    <a
                        href="{{ route('ttd.antrian') }}"
                        class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-primary-text hover:bg-primary-hover"
                    >
                        Proses & Generate PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection