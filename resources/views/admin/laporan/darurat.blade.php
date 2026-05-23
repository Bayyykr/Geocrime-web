<x-admin-layout>
    <x-slot name="header">Laporan Darurat</x-slot>

    @php
        $statusClass = [
            'aktif' => 'bg-gray-900 text-white',
            'dalam_penanganan' => 'bg-gray-200 text-gray-800',
        ];
    @endphp

    <div class="report-page emergency-monitor" data-active-sos-count="{{ $summary['active'] }}">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">{{ session('success') }}</div>
        @endif

        <div class="emergency-hero">
            <div>
                <p class="report-eyebrow">Emergency / SOS</p>
                <h1>Monitor Laporan Darurat Real-Time</h1>
                <p>Prioritas life-threatening: data GPS korban ditampilkan cepat, polsek terdekat dihitung otomatis, lalu admin bisa melakukan quick dispatch.</p>
            </div>
            <div class="emergency-pulse" aria-hidden="true">
                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.518 11.59c.75 1.334-.213 2.986-1.743 2.986H3.482c-1.53 0-2.493-1.652-1.743-2.986l6.518-11.59ZM11 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1-2a1 1 0 0 0 1-1V8a1 1 0 1 0-2 0v3a1 1 0 0 0 1 1Z" clip-rule="evenodd" /></svg>
            </div>
        </div>

        <div class="report-summary-grid">
            <div class="report-stat-card"><span>SOS Aktif</span><strong>{{ number_format($summary['active']) }}</strong><small>Belum dikirim personel</small></div>
            <div class="report-stat-card"><span>Dalam Penanganan</span><strong>{{ number_format($summary['handling']) }}</strong><small>Personel sudah dikirim</small></div>
            <div class="report-stat-card"><span>Masuk Hari Ini</span><strong>{{ number_format($summary['today']) }}</strong><small>Seluruh sinyal SOS</small></div>
            <div class="report-stat-card"><span>Selesai / Arsip</span><strong>{{ number_format($summary['done']) }}</strong><small>Masuk riwayat laporan</small></div>
        </div>

        <div class="emergency-map-panel">
            <div class="emergency-map-placeholder">
                @forelse ($items as $item)
                    <a class="sos-map-marker sos-marker-position-{{ ($loop->index % 8) + 1 }} {{ $item->status === 'aktif' ? 'is-blinking' : '' }}" target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}" title="{{ $item->kode_darurat }}">!</a>
                @empty
                    <div class="text-center text-sm font-semibold text-gray-500">Tidak ada sinyal SOS aktif.</div>
                @endforelse
            </div>
            <div class="emergency-map-info">
                <h2>Komponen visual dashboard</h2>
                <p>Pin merah berkedip adalah representasi titik SOS. Klik pin atau tombol Maps pada tabel untuk membuka koordinat GPS korban.</p>
                <button class="btn-secondary" type="button" data-test-alarm>Test Audio Alarm</button>
            </div>
        </div>

        <div class="report-table-card">
            <div class="report-section-header">
                <div>
                    <h2>Data Interaksi Real-Time</h2>
                    <p>Menampilkan SOS aktif dan laporan yang sedang dalam penanganan.</p>
                </div>
                <form class="flex flex-wrap gap-2" method="GET" action="{{ route('admin.laporan.darurat') }}">
                    <input class="master-search" name="search" value="{{ request('search') }}" placeholder="Cari kode, pelapor, polsek...">
                    <select class="form-select w-44" name="status">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="dalam_penanganan" @selected(request('status') === 'dalam_penanganan')>Dalam Penanganan</option>
                    </select>
                    <button class="btn-primary" type="submit">Filter</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="master-table emergency-table">
                    <thead>
                        <tr>
                            <th>ID Darurat</th>
                            <th>Status Live</th>
                            <th>Pelapor</th>
                            <th>Koordinat Instant</th>
                            <th>Polsek Terdekat</th>
                            <th>Response Time</th>
                            <th width="230">Quick Dispatch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr class="{{ $item->status === 'aktif' ? 'sos-row-active' : '' }}">
                                <td>
                                    <div class="font-black text-gray-950">{{ $item->kode_darurat }}</div>
                                    <div class="text-xs text-gray-500">SOS: {{ $item->waktu_sos?->format('d/m/Y H:i:s') ?? '-' }}</div>
                                </td>
                                <td>
                                    <span class="rounded-full px-2 py-1 text-xs font-bold {{ $statusClass[$item->status] ?? 'bg-gray-100 text-gray-600' }}">{{ str_replace('_', ' ', ucfirst($item->status)) }}</span>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $item->user?->name ?? 'Pelapor tidak login' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->user?->telepon ?? $item->user?->email ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="font-mono text-xs">{{ $item->latitude }}, {{ $item->longitude }}</div>
                                    <div class="max-w-[240px] truncate text-xs text-gray-500">{{ $item->alamat_terdeteksi ?: 'Alamat spesifik belum tersedia' }}</div>
                                </td>
                                <td>
                                    <div class="font-semibold">{{ $item->nearestPolsek?->nama ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->jarak_polsek_km !== null ? $item->jarak_polsek_km . ' km dari korban' : 'Jarak belum dihitung' }}</div>
                                </td>
                                <td>
                                    @if ($item->response_time_minutes !== null)
                                        <strong>{{ $item->response_time_minutes }} menit</strong>
                                        <div class="text-xs text-gray-500">Dispatch: {{ $item->waktu_dispatch?->format('H:i:s') }}</div>
                                    @else
                                        <span class="text-xs font-semibold text-gray-900">Menunggu dispatch</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <a class="btn-secondary" target="_blank" rel="noopener" href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}">Maps</a>
                                        @if ($item->status === 'aktif')
                                            <form method="POST" action="{{ route('admin.laporan.darurat.dispatch', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn-primary" type="submit">Kirim Personel</button>
                                            </form>
                                        @endif
                                        <button class="btn-secondary" type="button" data-modal-target="selesai-sos-{{ $item->id }}">Selesai</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-gray-500">Tidak ada laporan darurat aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $items->links() }}</div>
        </div>

        <details class="mt-5 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4">
            <summary class="cursor-pointer text-sm font-bold text-gray-700">Simulasi tombol SOS frontend</summary>
            <form class="mt-4 grid gap-3 md:grid-cols-4" method="POST" action="{{ route('admin.laporan.darurat.store') }}">
                @csrf
                <input class="form-input" name="latitude" placeholder="Latitude" value="-8.1336" required>
                <input class="form-input" name="longitude" placeholder="Longitude" value="113.2228" required>
                <input class="form-input md:col-span-2" name="alamat_terdeteksi" placeholder="Alamat/patokan otomatis dari device">
                <button class="btn-primary md:col-span-4" type="submit">Buat SOS Aktif</button>
            </form>
        </details>
    </div>

    @foreach ($items as $item)
        <div id="selesai-sos-{{ $item->id }}" class="modal-backdrop" hidden>
            <div class="modal-card modal-card-sm">
                <div class="modal-header">
                    <h2>Selesaikan SOS {{ $item->kode_darurat }}</h2>
                    <button type="button" data-modal-close="selesai-sos-{{ $item->id }}">×</button>
                </div>
                <form class="modal-body space-y-4" method="POST" action="{{ route('admin.laporan.darurat.complete', $item) }}">
                    @csrf
                    @method('PATCH')
                    <p class="rounded-xl bg-gray-100 p-3 text-sm text-gray-800">Setelah disimpan, data SOS ini otomatis masuk ke menu Riwayat Laporan sebagai rekam jejak.</p>
                    <div>
                        <label class="form-label" for="catatan-{{ $item->id }}">Catatan Akhir Petugas</label>
                        <textarea id="catatan-{{ $item->id }}" class="form-input" name="catatan_petugas" rows="4" placeholder="Contoh: Korban aman, personel Polsek Sumbersuko tiba di lokasi, situasi terkendali." required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary" type="button" data-modal-close="selesai-sos-{{ $item->id }}">Batal</button>
                        <button class="btn-primary" type="submit">Arsipkan ke Riwayat</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

</x-admin-layout>
