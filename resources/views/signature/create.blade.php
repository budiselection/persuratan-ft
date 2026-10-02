@extends('layouts.admin')

@section('title', 'Tanda Tangan Digital')

@section('content')
    <div class="mx-auto max-w-lg">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-neutral-900">
                Tanda Tangan Digital
            </h2>
            <p class="text-sm text-neutral-500">
                Unggah gambar tanda tangan Anda. Disarankan PNG transparan.
            </p>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
            @if (auth()->user()->signature_path)
                <div class="mb-4 rounded-md border border-success-border bg-success-background px-4 py-3 text-sm text-success-text">
                    Tanda tangan digital sudah tersimpan. Jika Anda mengunggah file baru, file lama akan diganti.
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('signature.store') }}"
                enctype="multipart/form-data"
                class="space-y-4"
            >
                @csrf

                <div>
                    <label
                        for="signature"
                        class="block text-sm font-medium text-neutral-700"
                    >
                        File Tanda Tangan
                    </label>

                    <input
                        type="file"
                        id="signature"
                        name="signature"
                        accept=".png,.jpg,.jpeg"
                        required
                        class="mt-2 block w-full text-sm text-neutral-500 file:mr-4 file:rounded-md file:border-0 file:bg-primary-base file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-hover"
                    >

                    <p class="mt-2 text-xs text-neutral-500">
                        Format: PNG, JPG, atau JPEG. Maksimal 1 MB.
                    </p>

                    @error('signature')
                        <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                >
                    Simpan Tanda Tangan
                </button>
            </form>
        </div>
    </div>
@endsection