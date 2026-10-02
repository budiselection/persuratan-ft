<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\NomorSuratController;
use App\Http\Controllers\TandaTanganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\VerifikasiPublikController;
use App\Http\Controllers\JenisSuratController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Upload tanda tangan digital
    Route::get('/signature', [SignatureController::class, 'create'])->name('signature.create');
    Route::post('/signature', [SignatureController::class, 'store'])->name('signature.store');

    // Download PDF final
    Route::get('/pengajuan/{pengajuan}/download', [PengajuanSuratController::class, 'download'])
        ->middleware('can:download,pengajuan')
        ->name('pengajuan.download');

    // Admin Fakultas
    Route::middleware(['role:Admin Fakultas|Super Admin'])->group(function () {
        Route::resource('pengajuan', PengajuanSuratController::class)->except('show');
        Route::post('/pengajuan/{pengajuan}/submit', [PengajuanSuratController::class, 'submit'])->name('pengajuan.submit');
    });

    // Detail pengajuan: terbuka untuk semua role login, otorisasi lewat Policy view
    Route::get('/pengajuan/{pengajuan}', [PengajuanSuratController::class, 'show'])
        ->whereNumber('pengajuan')
        ->name('pengajuan.show');

    Route::get('/pengajuan/{pengajuan}/detail', [PengajuanSuratController::class, 'show'])
        ->whereNumber('pengajuan')
        ->name('pengajuan.detail');

    // BAAK
    Route::middleware(['role:BAAK|Super Admin'])->group(function () {
        Route::get('/baak/antrian', [NomorSuratController::class, 'index'])->name('baak.antrian');
        Route::get('/baak/{pengajuan}/preview-nomor', [NomorSuratController::class, 'preview'])->name('baak.preview');
        Route::post('/baak/{pengajuan}/generate-nomor', [NomorSuratController::class, 'generate'])->name('baak.generate');

        Route::post('/verifikasi/{pengajuan}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
        Route::post('/verifikasi/{pengajuan}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');
    });

    // Penandatangan
    Route::middleware(['role:Penandatangan|Super Admin'])->group(function () {
        Route::get('/ttd/antrian', [TandaTanganController::class, 'index'])->name('ttd.antrian');
        Route::get('/ttd/{pengajuan}/preview', [TandaTanganController::class, 'preview'])->name('ttd.preview');
        Route::post('/ttd/{pengajuan}/sign', [TandaTanganController::class, 'sign'])->name('ttd.sign');
    });

    // Super Admin: master data + template + user
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::resource('jenis-surat', JenisSuratController::class)
            ->except('show')
            ->parameters(['jenis-surat' => 'jenisSurat']);

        Route::get('/jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'templateForm'])
            ->name('jenis-surat.template');

        Route::post('/jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'uploadTemplate'])
            ->name('jenis-surat.template.store');

        Route::delete('/jenis-surat/{jenisSurat}/template', [JenisSuratController::class, 'deleteTemplate'])
            ->name('jenis-surat.template.destroy');

        Route::get('/jenis-surat/{jenisSurat}/preview-template', [JenisSuratController::class, 'previewTemplate'])
            ->name('jenis-surat.template.preview');

        // Editor template online (PERBAIKAN: sebelumnya di luar group Super Admin)
        Route::get('/jenis-surat/{jenisSurat}/template/edit', [JenisSuratController::class, 'editTemplate'])
            ->name('jenis-surat.template.edit');

        Route::post('/jenis-surat/{jenisSurat}/template/edit', [JenisSuratController::class, 'updateTemplate'])
            ->name('jenis-surat.template.update');

        Route::resource('users', UserController::class)->except('show');
    });

    // Laporan
    Route::middleware(['role:Super Admin|Admin Fakultas|BAAK'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

        Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])
            ->middleware('throttle:laporan-export')
            ->name('laporan.export.csv');

        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])
            ->middleware('throttle:laporan-export')
            ->name('laporan.export.pdf');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notificationId}/read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
});

// Verifikasi publik melalui QR code
Route::get('/verifikasi/{qr_token}', [VerifikasiPublikController::class, 'show'])
    ->middleware('throttle:verifikasi-publik')
    ->whereUuid('qr_token')
    ->name('verifikasi.publik');

require __DIR__.'/auth.php';