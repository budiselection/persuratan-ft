@php
    $jenisSurat = $jenisSurat ?? null;

    // Ambil data field dari old input atau database
    $rawFields = old('fields_json', $jenisSurat?->fields_json ?? []);

    // Pastikan setiap elemen adalah array, lalu format ulang
    $initialFields = collect($rawFields)
        ->filter(function ($field) {
            return is_array($field);
        })
        ->map(function ($field) {
            return [
                'name' => $field['name'] ?? '',
                'label' => $field['label'] ?? '',
                'type' => $field['type'] ?? 'text',
                'required' => filter_var($field['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        })
        ->values()
        ->all();
@endphp

<div x-data="jenisSuratForm()" class="space-y-6" x-cloak>
    {{-- Informasi Jenis Surat --}}
    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-neutral-900">Informasi Jenis Surat</h3>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label for="nama" class="block text-sm font-medium text-neutral-700">
                    Nama Jenis Surat <span class="text-danger-base">*</span>
                </label>
                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $jenisSurat?->nama) }}"
                    required
                    class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                >
                @error('nama')
                    <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="kode" class="block text-sm font-medium text-neutral-700">
                    Kode Surat <span class="text-danger-base">*</span>
                </label>
                <input
                    type="text"
                    id="kode"
                    name="kode"
                    value="{{ old('kode', $jenisSurat?->kode) }}"
                    required
                    class="mt-2 w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                >
                @error('kode')
                    <p class="mt-2 text-sm text-danger-base">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Field Form Dinamis --}}
    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-neutral-900">Field Form Dinamis</h3>
                <p class="mt-1 text-sm text-neutral-500">
                    Field ini akan muncul saat admin membuat pengajuan surat.
                </p>
            </div>

            <button
                type="button"
                @click="addField()"
                class="rounded-md bg-primary-base px-3 py-2 text-xs font-semibold text-white hover:bg-primary-hover"
            >
                Tambah Field
            </button>
        </div>

        <div class="mt-4 space-y-3">
            <template x-for="(field, index) in fields" :key="index">
                <div class="grid grid-cols-1 gap-3 rounded-md border border-neutral-200 p-4 md:grid-cols-12">
                    <div class="md:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-neutral-500">Nama Field</label>
                        <input
                            type="text"
                            x-model="field.name"
                            :name="`fields_json[${index}][name]`"
                            placeholder="contoh: nim"
                            class="w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                        >
                    </div>

                    <div class="md:col-span-3">
                        <label class="mb-1 block text-xs font-medium text-neutral-500">Label</label>
                        <input
                            type="text"
                            x-model="field.label"
                            :name="`fields_json[${index}][label]`"
                            placeholder="contoh: NIM"
                            class="w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="mb-1 block text-xs font-medium text-neutral-500">Tipe Input</label>
                        <select
                            x-model="field.type"
                            :name="`fields_json[${index}][type]`"
                            class="w-full rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
                        >
                            <option value="text">Text</option>
                            <option value="textarea">Textarea</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="email">Email</option>
                            <option value="tel">Telepon</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-4 md:col-span-3">
                        <label class="flex items-center gap-2 text-sm text-neutral-700">
                            <input
                                type="hidden"
                                :name="`fields_json[${index}][required]`"
                                value="0"
                            >
                            <input
                                type="checkbox"
                                x-model="field.required"
                                :name="`fields_json[${index}][required]`"
                                value="1"
                                class="rounded border-neutral-300 text-primary-base focus:ring-primary-focus"
                            >
                            Wajib
                        </label>

                        <button
                            type="button"
                            @click="removeField(index)"
                            class="text-xs font-semibold text-danger-base hover:text-danger-text"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </template>

            <div x-show="fields.length === 0" class="rounded-md border border-dashed border-neutral-300 p-6 text-center text-sm text-neutral-500">
                Belum ada field. Klik tombol "Tambah Field".
            </div>
        </div>

        @error('fields_json')
            <p class="mt-3 text-sm text-danger-base">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    function jenisSuratForm() {
        return {
            fields: @json($initialFields ?? []),

            init() {
                console.log('Alpine initialized. Fields:', this.fields);
            },

            addField() {
                console.log('addField() dipanggil');
                this.fields.push({
                    name: '',
                    label: '',
                    type: 'text',
                    required: false
                });
            },

            removeField(index) {
                this.fields.splice(index, 1);
            }
        };
    }
</script>