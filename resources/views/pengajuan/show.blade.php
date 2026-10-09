@extends('layouts.admin')

@section('title', 'Detail Pengajuan')

@section('content')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Kolom kiri --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-neutral-900">
                            Detail Pengajuan
                        </h2>
                        <p class="mt-1 text-sm text-neutral-500">
                            No Tiket: {{ $pengajuan->no_tiket }}
                        </p>
                    </div>

                    @include('partials.status-badge', ['status' => $pengajuan->status])
                </div>

                <dl class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Jenis Surat</dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ $pengajuan->jenisSurat?->nama }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Pemohon</dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ $pengajuan->pemohon?->name }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Nomor Surat</dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ $pengajuan->nomor_surat ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-neutral-500">Tanggal Dibuat</dt>
                        <dd class="mt-1 text-sm text-neutral-900">
                            {{ $pengajuan->created_at->format('d M Y H:i') }}
                        </dd>
                    </div>

                    @if ($pengajuan->targetSigner)
                        <div>
                            <dt class="text-sm font-medium text-neutral-500">Target Penandatangan</dt>
                            <dd class="mt-1 text-sm text-neutral-900">
                                {{ $pengajuan->targetSigner->name }}
                                <span class="block text-xs text-neutral-500">
                                    NIP: {{ $pengajuan->targetSigner->nip ?? '-' }}
                                </span>
                            </dd>
                        </div>
                    @endif

                    @if ($pengajuan->penandatangan)
                        <div>
                            <dt class="text-sm font-medium text-neutral-500">Penandatangan (Aktual)</dt>
                            <dd class="mt-1 text-sm text-neutral-900">
                                {{ $pengajuan->penandatangan->name }}
                            </dd>
                        </div>
                    @endif

                    @if ($pengajuan->tanggal_ttd)
                        <div>
                            <dt class="text-sm font-medium text-neutral-500">Tanggal TTD</dt>
                            <dd class="mt-1 text-sm text-neutral-900">
                                {{ $pengajuan->tanggal_ttd->format('d M Y H:i') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">
                    Data Isian Surat
                </h3>

                <div class="mt-4 divide-y divide-neutral-100">
                    @foreach ($pengajuan->data_json ?? [] as $key => $value)
                        <div class="grid grid-cols-1 gap-2 py-3 md:grid-cols-3">
                            <div class="text-sm font-medium text-neutral-500">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </div>

                            <div class="text-sm text-neutral-900 md:col-span-2">
                                {{ is_array($value) ? implode(', ', $value) : $value }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">
                    Lampiran
                </h3>

                <div class="mt-4 space-y-2">
                    @forelse ($pengajuan->lampiran as $lampiran)
                        <a
                            href="{{ asset('storage/' . $lampiran->file_path) }}"
                            target="_blank"
                            class="flex items-center justify-between rounded-md border border-neutral-200 px-4 py-3 text-sm text-neutral-700 hover:bg-neutral-50"
                        >
                            <span>{{ $lampiran->jenis }}</span>
                            <span class="text-xs text-primary-base">Lihat File</span>
                        </a>
                    @empty
                        <p class="text-sm text-neutral-500">
                            Tidak ada lampiran.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Riwayat Aktivitas --}}
            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">
                    Riwayat Aktivitas
                </h3>

                <div class="mt-4 space-y-4">
                    @forelse ($pengajuan->logAktivitas as $log)
                        <div class="border-l-2 border-neutral-200 pl-4">
                            <p class="text-sm font-medium text-neutral-900">
                                {{ ucfirst(str_replace('_', ' ', $log->aksi)) }}
                            </p>

                            <p class="mt-1 text-sm text-neutral-600">
                                {{ $log->keterangan }}
                            </p>

                            <p class="mt-1 text-xs text-neutral-400">
                                {{ $log->user?->name }} - {{ $log->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500">
                            Belum ada riwayat aktivitas.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-6">
            @if ($pengajuan->catatan_revisi)
                <div class="rounded-lg border border-warning-border bg-warning-background p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-warning-text">
                        Catatan Revisi / Penolakan
                    </h3>

                    <p class="mt-2 text-sm text-warning-text">
                        {{ $pengajuan->catatan_revisi }}
                    </p>
                </div>
            @endif

            {{-- Kartu Aksi Proses (tunggal, tidak duplikat) --}}
            <div
                x-data="{ openReject: false }"
                class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
            >
                <h3 class="text-sm font-semibold text-neutral-900">
                    Aksi Proses
                </h3>

                <div class="mt-4 space-y-3">
                    @can('update', $pengajuan)
    <a
        href="{{ route('pengajuan.edit', $pengajuan) }}"
        class="block w-full rounded-md border border-primary-border bg-primary-extra-light px-4 py-2 text-center text-sm font-semibold text-primary-base hover:bg-primary-light"
    >
        Edit Draft
    </a>
@endcan
                    {{-- Tombol Submit ke BAAK (Admin Fakultas) --}}
                    @can('update', $pengajuan)
                        <form
                            method="POST"
                            action="{{ route('pengajuan.submit', $pengajuan) }}"
                            onsubmit="return confirm('Ajukan surat ini untuk diproses?')"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                            >
                                Submit ke BAAK
                            </button>
                        </form>
                    @endcan

                    {{-- ★★★ FORM EDIT NOMOR SURAT (BAAK) ★★★ --}}
                    @can('generateNomor', $pengajuan)
                        <form
                            method="POST"
                            action="{{ route('baak.generate', $pengajuan) }}"
                            class="space-y-3 rounded-md border border-neutral-200 bg-neutral-50 p-4"
                        >
                            @csrf

                            <label for="nomor_manual" class="block text-sm font-medium text-neutral-700">
                                {{ $pengajuan->nomor_surat ? 'Edit Nomor Surat' : 'Tetapkan Nomor Surat' }}
                                @if (! config('surat.auto_number_enabled'))
                                    <span class="text-danger-base">*</span>
                                @endif
                            </label>

                            <input
                                type="text"
                                id="nomor_manual"
                                name="nomor_manual"
                                value="{{ old('nomor_manual', $pengajuan->nomor_surat) }}"
                                placeholder="Contoh: 148.A-9/D-TEK/VIII/2026"
                                class="w-full rounded-md border-neutral-300 font-mono text-sm focus:border-primary-focus focus:ring-primary-focus"
                            >

                            @error('nomor_manual')
                                <p class="text-sm text-danger-base">{{ $message }}</p>
                            @enderror

                            <button
                                type="submit"
                                class="w-full rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-primary-text hover:bg-primary-hover"
                            >
                                {{ $pengajuan->nomor_surat ? 'Perbarui Nomor' : 'Simpan Nomor' }}
                            </button>

                            <p class="text-xs text-neutral-500">
                                @if (config('surat.auto_number_enabled'))
                                    Kosongkan untuk menggunakan nomor otomatis.
                                @else
                                    Nomor otomatis nonaktif — wajib diisi manual.
                                @endif
                            </p>
                        </form>
                    @endcan

                    {{-- Tombol Approve (BAAK) --}}
                    @can('verify', $pengajuan)
                        <form
                            method="POST"
                            action="{{ route('verifikasi.approve', $pengajuan) }}"
                            onsubmit="return confirm('Setujui pengajuan ini dan kirim ke penandatangan?')"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-md bg-success-base px-4 py-2 text-sm font-semibold text-white hover:bg-success-hover"
                            >
                                Approve & Kirim ke Penandatangan
                            </button>
                        </form>

                        <button
                            type="button"
                            @click="openReject = true"
                            class="w-full text-center text-xs font-semibold text-danger-base underline hover:text-danger-hover"
                        >
                            Tolak / Kembalikan ke Admin (Revisi)
                        </button>
                    @endcan

                    {{-- Tombol Tanda Tangani (Penandatangan) --}}
                    @can('sign', $pengajuan)
                        <form
                            method="POST"
                            action="{{ route('ttd.sign', $pengajuan) }}"
                            onsubmit="return confirm('Tanda tangani surat ini?')"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-md bg-secondary-base px-4 py-2 text-sm font-semibold text-secondary-text hover:bg-secondary-hover"
                            >
                                Tanda Tangani
                            </button>
                        </form>
                    @endcan

                    {{-- Tombol Download PDF --}}
                    @can('download', $pengajuan)
                        <a
                            href="{{ route('pengajuan.download', $pengajuan) }}"
                            class="block w-full rounded-md bg-primary-base px-4 py-2 text-center text-sm font-semibold text-white hover:bg-primary-hover"
                        >
                            Download PDF
                        </a>
                    @endcan
                </div>

                {{-- Modal Reject --}}
                <div
                    x-show="openReject"
                    x-transition
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    style="display: none;"
                >
                    <div
                        class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                        @click.outside="openReject = false"
                    >
                        <h3 class="text-lg font-semibold text-neutral-900">
                            Tolak / Kembalikan untuk Revisi
                        </h3>

                        <form
                            method="POST"
                            action="{{ route('verifikasi.reject', $pengajuan) }}"
                            class="mt-4 space-y-4"
                        >
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-neutral-700">
                                    Alasan Revisi <span class="text-danger-base">*</span>
                                </label>
                                <textarea
                                    name="catatan_revisi"
                                    rows="4"
                                    required
                                    class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                                    placeholder="Jelaskan alasan penolakan atau revisi yang diperlukan..."
                                >{{ old('catatan_revisi') }}</textarea>

                                @error('catatan_revisi')
                                    <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="openReject = false"
                                    class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="rounded-md bg-danger-base px-4 py-2 text-sm font-semibold text-white hover:bg-danger-hover"
                                >
                                    Kirim Penolakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Link Verifikasi Publik (status Selesai) --}}
            @if ($pengajuan->status === \App\Enums\StatusPengajuan::SELESAI && $pengajuan->qr_token)
                <a
                    href="{{ route('verifikasi.publik', $pengajuan->qr_token) }}"
                    target="_blank"
                    class="block w-full rounded-md border border-neutral-300 bg-white px-4 py-2 text-center text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                >
                    Lihat Halaman Verifikasi
                </a>
            @endif
        </div>
    </div>
@endsection