<?php

namespace App\Http\Controllers;

use App\Enums\StatusPengajuan;
use App\Models\LogAktivitas;
use App\Models\PengajuanSurat;
use App\Services\SuratPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TandaTanganController extends Controller
{
    public function index()
{
    $userId = auth()->id();

    $antrian = PengajuanSurat::query()
        ->with(['jenisSurat', 'pemohon', 'targetSigner'])
        ->where('status', StatusPengajuan::MENUNGGU_TTD)
        ->where(function ($query) use ($userId) {
            $query->where('target_signer_id', $userId)
                ->orWhereNull('target_signer_id');
        })
        ->latest()
        ->paginate(10);

    return view('ttd.index', compact('antrian'));
}

    public function preview(
        Request $request,
        PengajuanSurat $pengajuan,
        SuratPdfService $pdfService
    ) {
        $this->authorize('sign', $pengajuan);

        $signer = $request->user();

        if (! $signer->signature_path) {
            return redirect()
                ->route('signature.create')
                ->with('error', 'Silakan unggah tanda tangan digital terlebih dahulu.');
        }

        $content = $pdfService->render($pengajuan, $signer);

        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="preview-' . $pengajuan->no_tiket . '.pdf"');
    }

    public function sign(
        Request $request,
        PengajuanSurat $pengajuan,
        SuratPdfService $pdfService
    ) {
        $this->authorize('sign', $pengajuan);

        $signer = $request->user();

        if (! $signer->signature_path) {
            return redirect()
                ->route('signature.create')
                ->with('error', 'Silakan unggah tanda tangan digital terlebih dahulu.');
        }

        if (blank($pengajuan->nomor_surat)) {
            return back()->with('error', 'Nomor surat belum tersedia.');
        }

        $disk = Storage::disk(config('surat.pdf_disk', 'local'));
        $path = null;

        try {
            $path = $pdfService->finalize($pengajuan, $signer);

            DB::transaction(function () use ($pengajuan, $signer, $path, $request) {
                $pengajuan->forceFill([
                    'penandatangan_id' => $signer->id,
                    'tanggal_ttd' => now(),
                    'file_pdf' => $path,
                    'status' => StatusPengajuan::SELESAI,
                ])->save();

                LogAktivitas::create([
                    'pengajuan_id' => $pengajuan->id,
                    'user_id' => $request->user()->id,
                    'aksi' => 'tanda_tangan',
                    'keterangan' => 'Surat ditandatangani dan PDF final dibuat.',
                ]);
            });
        } catch (Throwable $e) {
            if ($path && $disk->exists($path)) {
                $disk->delete($path);
            }

            report($e);

            return back()->with('error', 'Gagal menandatangani surat. Silakan coba lagi.');
        }

        return redirect()
            ->route('pengajuan.show', $pengajuan)
            ->with('success', 'Surat berhasil ditandatangani dan selesai.');
    }
}