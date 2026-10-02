@extends('layouts.admin')

@section('title', 'Edit Jenis Surat')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-neutral-900">
                Edit Jenis Surat
            </h2>
            <p class="text-sm text-neutral-500">
                Perbarui data jenis surat dan field form dinamis.
            </p>
        </div>

        <form
            method="POST"
            action="{{ route('jenis-surat.update', $jenisSurat) }}"
        >
            @csrf
            @method('PUT')

            @include('jenis-surat._form', ['jenisSurat' => $jenisSurat])

            <div class="mt-6 flex justify-end gap-3">
                <a
                    href="{{ route('jenis-surat.index') }}"
                    class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-semibold text-neutral-700 hover:bg-neutral-50"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-md bg-primary-base px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection