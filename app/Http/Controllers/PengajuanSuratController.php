<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\JenisSurat;
use App\Models\Lampiran;
use App\Models\LogAktivitas;
use App\Enums\StatusPengajuan;
use App\Http\Requests\StorePengajuanRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;


class PengajuanSuratController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $query = PengajuanSurat::query()
            ->with(['jenisSurat', 'pemohon'])
            ->latest();

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('jenis_surat_id')) {
            $query->where('jenis_surat_id', request('jenis_surat_id'));
        }

        $pengajuanList = $query->paginate(10)->withQueryString();

        return view('pengajuan.index', compact('pengajuanList'));
    }

    public function show(PengajuanSurat $pengajuan)
{
    $this->authorize('view', $pengajuan);

    $pengajuan->load([
        'jenisSurat',
        'pemohon',
        'penandatangan',
        'targetSigner',
        'lampiran',
        'logAktivitas.user',
    ]);

    return view('pengajuan.show', compact('pengajuan'));
}
    public function create()
    {
        $jenisSurat = JenisSurat::all();
        $penandatangan = User::query()
    ->role(['Penandatangan', 'Super Admin'])
    ->where('is_active', true)
    ->orderBy('name')
    ->get();

    $jenisListData = $jenisSurat->map(function ($item) {
        return [
            'id' => (int) $item->id,
            'nama' => $item->nama,
            'fields' => array_values($item->fields_json ?? []),
        ];
    })->values()->all();

    return view('pengajuan.create', [
        'jenisSurat' => $jenisSurat,
        'jenisListData' => $jenisListData,
        'penandatangan' => $penandatangan,
    ]);
    }

    public function store(StorePengajuanRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Simpan Data Pengajuan
            $pengajuan = PengajuanSurat::create([
                'jenis_surat_id' => $request->jenis_surat_id,
                'user_id' => Auth::id(),
                'data_json' => $request->data_json,
                'status' => StatusPengajuan::MENGAJUKAN,
            ]);

            // 2. Handle Upload Lampiran
            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran') as $index => $file) {
                    $path = $file->store('lampiran/' . $pengajuan->id, 'public');
                    Lampiran::create([
                        'pengajuan_id' => $pengajuan->id,
                        'file_path' => $path,
                        'jenis' => 'dokumen_pendukung_' . ($index + 1),
                    ]);
                }
            }

            // 3. Catat Log Aktivitas
            LogAktivitas::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id' => Auth::id(),
                'aksi' => 'create',
                'keterangan' => 'Membuat pengajuan surat baru (Draft).',
            ]);
        });

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil disimpan sebagai Draft.');
    }
    
    // Method submit untuk mengubah status dari Draft ke Menunggu Nomor
    public function submit(PengajuanSurat $pengajuan)
    {
        
        $this->authorize('update', $pengajuan); // Cek Policy
        
        $pengajuan->update(['status' => StatusPengajuan::MENUNGGU_NOMOR]);
        
        LogAktivitas::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id' => Auth::id(),
            'aksi' => 'submit',
            'keterangan' => 'Mengajukan surat untuk diproses BAAK.',
        ]);

        return back()->with('success', 'Surat berhasil diajukan.');
    }
    public function download(PengajuanSurat $pengajuan)
{
    $this->authorize('download', $pengajuan);

    if (! $pengajuan->file_pdf) {
        abort(404, 'PDF final belum tersedia.');
    }

    $disk = Storage::disk(config('surat.pdf_disk', 'local'));

    if (! $disk->exists($pengajuan->file_pdf)) {
        abort(404, 'File PDF tidak ditemukan.');
    }

   
    $nomorSafe = str_replace(['/', '\\', ' '], '-', $pengajuan->nomor_surat ?? $pengajuan->no_tiket);

    $filename = sprintf(
        '%s-%s.pdf',
        $nomorSafe,
        $pengajuan->jenisSurat?->kode ?? 'surat'
    );

    return response()->download(
        $disk->path($pengajuan->file_pdf),
        $filename
    );
}
}