@extends('layouts.admin')

@section('title', 'Edit Pengajuan')

@section('content')
    <div
        x-data="pengajuanForm(@js($jenisListData), @js(old('data_json', $pengajuan->data_json ?? [])), @js($pengajuan->jenis_surat_id))"
        class="mx-auto max-w-4xl"
    >
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-neutral-900">Edit Pengajuan</h2>
            <p class="text-sm text-neutral-500">
                No Tiket: {{ $pengajuan->no_tiket }}
            </p>
        </div>

        @if ($pengajuan->status === \App\Enums\StatusPengajuan::REVISI && $pengajuan->catatan_revisi)
            <div class="mb-4 rounded-md border border-warning-border bg-warning-background px-4 py-3 text-sm text-warning-text">
                <p class="font-semibold">Catatan Revisi dari BAAK:</p>
                <p class="mt-1">{{ $pengajuan->catatan_revisi }}</p>
            </div>
        @endif

        @php $dataErrors = Arr::flatten($errors->get('data_json.*')); @endphp
        @if (count($dataErrors))
            <div class="mb-4 rounded-md border border-danger-border bg-danger-background px-4 py-3 text-sm text-danger-text">
                <ul class="list-inside list-disc">
                    @foreach ($dataErrors as $msg)
                        <li>{{ $msg }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('pengajuan.update', $pengajuan) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">Informasi Dasar</h3>

                <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Jenis Surat</label>
                        <input
                            type="text"
                            value="{{ $pengajuan->jenisSurat?->nama }}"
                            disabled
                            class="mt-2 w-full rounded-md border-neutral-200 bg-neutral-100 text-sm text-neutral-500"
                        >
                        <input type="hidden" name="jenis_surat_id" value="{{ $pengajuan->jenis_surat_id }}">
                        <p class="mt-1 text-xs text-neutral-500">Jenis surat tidak dapat diubah setelah draft dibuat.</p>
                    </div>

                    <div>
                        <label for="target_signer_id" class="block text-sm font-medium text-neutral-700">
                            Penandatangan <span class="text-danger-base">*</span>
                        </label>
                        <select
                            id="target_signer_id"
                            name="target_signer_id"
                            required
                            class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                        >
                            @foreach ($penandatangan as $calon)
                                <option
                                    value="{{ $calon->id }}"
                                    @selected(old('target_signer_id', $pengajuan->target_signer_id) == $calon->id)
                                >
                                    {{ $calon->name }} — NIP: {{ $calon->nip ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('target_signer_id')
                            <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">Data Isian Surat</h3>

                <div class="mt-4 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <template x-for="field in (selectedJenis ? selectedJenis.fields : [])" :key="field.name">
                        <div :class="field.type === 'textarea' ? 'md:col-span-2' : ''">
                            <label class="block text-sm font-medium text-neutral-700">
                                <span x-text="field.label"></span>
                                <span x-show="field.required" class="text-danger-base">*</span>
                            </label>

                            <textarea
                                x-show="field.type === 'textarea'"
                                :name="`data_json[${field.name}]`"
                                x-model="data[field.name]"
                                rows="3"
                                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                            ></textarea>

                            <input
                                x-show="field.type !== 'textarea'"
                                :type="field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text')"
                                :name="`data_json[${field.name}]`"
                                x-model="data[field.name]"
                                class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                            >
                        </div>
                    </template>
                </div>
            </div>

            <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-neutral-900">Lampiran</h3>

                @if ($pengajuan->lampiran->count())
                    <ul class="mt-3 space-y-2">
                        @foreach ($pengajuan->lampiran as $lampiran)
                            <li class="flex items-center justify-between rounded-md border border-neutral-200 px-4 py-2 text-sm">
                                <span class="text-neutral-700">{{ $lampiran->jenis }}</span>
                                <a href="{{ asset('storage/'.$lampiran->file_path) }}" target="_blank" class="text-xs font-semibold text-primary-base">Lihat</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <input
                    type="file"
                    name="lampiran[]"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-3 w-full text-sm text-neutral-600 file:mr-4 file:rounded-md file:border-0 file:bg-primary-base file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-text hover:file:bg-primary-hover"
                >
                <p class="mt-1 text-xs text-neutral-500">File baru akan ditambahkan ke lampiran existing.</p>
                @error('lampiran.*')
                    <p class="mt-1 text-sm text-danger-base">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('pengajuan.index') }}"
                    class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    name="submit_action"
                    value="draft"
                    class="rounded-md border border-primary-border bg-primary-extra-light px-4 py-2 text-sm font-semibold text-primary-base hover:bg-primary-light"
                >
                    Simpan Draft
                </button>
                <button
                    type="submit"
                    name="submit_action"
                    value="submit"
                    onclick="return confirm('Ajukan surat ini untuk diproses BAAK?')"
                    class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-primary-text hover:bg-primary-hover"
                >
                    Ajukan Surat
                </button>
            </div>
        </form>
    </div>

    <script>
        function pengajuanForm(jenisList, initialData, initialJenisId) {
            return {
                jenisList: jenisList,
                data: initialData || {},
                jenisId: initialJenisId || '',
                get selectedJenis() {
                    return this.jenisList.find(j => String(j.id) === String(this.jenisId));
                },
            };
        }
    </script>
@endsection