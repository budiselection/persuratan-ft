@extends('layouts.admin')

@section('title', 'Editor Template')

@section('content')
    <div class="mx-auto max-w-6xl">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-neutral-900">
                    Editor Template: {{ $jenisSurat->nama }}
                </h2>
                <p class="text-sm text-neutral-500">
                    Edit isi surat langsung dari browser. Perubahan tersimpan tanpa mengubah kode aplikasi.
                </p>
            </div>

            <a
                href="{{ route('jenis-surat.template.preview', $jenisSurat) }}"
                target="_blank"
                class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
            >
                Preview PDF
            </a>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- Editor --}}
            <div class="xl:col-span-2">
                <form
                    method="POST"
                    action="{{ route('jenis-surat.template.update', $jenisSurat) }}"
                    class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm"
                >
                    @csrf

                    <label class="block text-sm font-medium text-neutral-700">
                        Kode Template HTML
                    </label>

                    <textarea
                        name="template_html"
                        rows="30"
                        class="mt-2 w-full rounded-md border-neutral-300 font-mono text-xs leading-relaxed focus:border-primary-focus focus:ring-primary-focus"
                    >{{ $content }}</textarea>

                    @error('template_html')
                        <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                    @enderror

                    <div class="mt-4 flex justify-end gap-3">
                        <a
                            href="{{ route('jenis-surat.template', $jenisSurat) }}"
                            class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                        >
                            Kembali
                        </a>

                        <button
                            type="submit"
                            class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                        >
                            Simpan Template
                        </button>
                    </div>
                </form>
            </div>

            {{-- Daftar Placeholder --}}
            <div class="space-y-6">
                <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900">
                        Placeholder Tersedia
                    </h3>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($knownPlaceholders as $placeholder)
                            <span class="rounded bg-neutral-100 px-2 py-1 font-mono text-xs text-neutral-700">
                                {{ chr(123) . chr(123) . $placeholder . chr(125) . chr(125) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                @if (count($unknownPlaceholders))
                    <div class="rounded-lg border border-warning-border bg-warning-background p-6 shadow-sm">
                        <h3 class="text-sm font-semibold text-warning-text">
                            Placeholder Tidak Dikenali
                        </h3>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($unknownPlaceholders as $placeholder)
                                <span class="rounded bg-warning-background px-2 py-1 font-mono text-xs text-warning-text">
                                    {{ chr(123) . chr(123) . $placeholder . chr(125) . chr(125) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-semibold text-neutral-900">
                        Tips
                    </h3>
                    <ul class="mt-3 list-inside list-disc space-y-1 text-sm text-neutral-600">
                        <li>Data per surat gunakan placeholder field.</li>
                        <li>Tanda tangan digital: letakkan placeholder signature di blok pejabat yang menandatangani.</li>
                        <li>Teks kebijakan (menimbang, mengingat, ketentuan) tulis langsung di template agar mudah direvisi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection