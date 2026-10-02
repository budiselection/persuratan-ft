@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-neutral-900">
                Dashboard Persuratan
            </h2>
            <p class="text-sm text-neutral-500">
                Statistik pengajuan surat tahun {{ $tahun }}.
            </p>
        </div>

        <form method="GET" action="{{ route('dashboard') }}">
            <select
                name="tahun"
                onchange="this.form.submit()"
                class="rounded-md border-neutral-300 text-sm focus:border-primary-focus focus:ring-primary-focus"
            >
                @foreach (range(now()->year, now()->subYears(3)->year) as $item)
                    <option
                        value="{{ $item }}"
                        @selected($item == $tahun)
                    >
                        {{ $item }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-neutral-500">Total Pengajuan</p>
            <p class="mt-1 text-2xl font-semibold text-neutral-900">
                {{ $statistikStatus->sum() }}
            </p>
        </div>

        @foreach ($statistikStatus as $label => $total)
            <div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-neutral-500">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold text-neutral-900">
                    {{ $total }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm xl:col-span-2">
            <h3 class="text-sm font-semibold text-neutral-900">
                Grafik Pengajuan Bulanan
            </h3>

            <div class="mt-4">
                <canvas id="chartBulanan" height="110"></canvas>
            </div>
        </div>

        <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-neutral-900">
                Jenis Surat Terbanyak
            </h3>

            <div class="mt-4 space-y-3">
                @forelse ($statistikJenis as $nama => $total)
                    <div class="flex items-center justify-between rounded-md border border-neutral-100 px-3 py-2">
                        <p class="text-sm text-neutral-700">{{ $nama }}</p>
                        <p class="text-sm font-semibold text-neutral-900">{{ $total }}</p>
                    </div>
                @empty
                    <p class="text-sm text-neutral-500">
                        Belum ada data.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-neutral-200 bg-white shadow-sm">
        <div class="border-b border-neutral-200 px-6 py-4">
            <h3 class="text-sm font-semibold text-neutral-900">
                Pengajuan Terbaru
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm">
                <thead class="bg-neutral-50 text-left text-xs font-semibold uppercase tracking-wide text-neutral-500">
                    <tr>
                        <th class="px-6 py-3">No Tiket</th>
                        <th class="px-6 py-3">Jenis Surat</th>
                        <th class="px-6 py-3">Pemohon</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-neutral-100 bg-white">
                    @forelse ($recent as $item)
                        <tr>
                            <td class="px-6 py-4 font-medium text-neutral-900">
                                {{ $item->no_tiket }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->jenisSurat?->nama }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $item->pemohon?->name }}
                            </td>

                            <td class="px-6 py-4">
                                @include('partials.status-badge', ['status' => $item->status])
                            </td>

                            <td class="px-6 py-4 text-neutral-600">
                                {{ $item->created_at->format('d M Y H:i') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a
                                    href="{{ route('pengajuan.show', $item) }}"
                                    class="text-xs font-semibold text-primary-base hover:text-primary-hover"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-neutral-500">
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('chartBulanan');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($bulanLabels),
                    datasets: [
                        {
                            label: 'Jumlah Pengajuan',
                            data: @json(array_values($bulananData)),
                            backgroundColor: '#11667B',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection