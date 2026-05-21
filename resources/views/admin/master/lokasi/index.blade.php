<x-admin-layout>
    <x-slot name="header">Lokasi</x-slot>

    @php
        $toastType = session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : null);
        $toastMessage = session('success') ?: session('error') ?: ($errors->any() ? $errors->first() : null);
        $statusOptions = ['Aman', 'Rawan', 'Sangat Rawan'];
        $statusBadgeClass = [
            'Aman' => 'bg-green-100 text-green-700',
            'Rawan' => 'bg-yellow-100 text-yellow-700',
            'Sangat Rawan' => 'bg-red-100 text-red-700',
        ];
    @endphp

    @if ($toastType && $toastMessage)
        <div class="toast-notification {{ $toastType }}" data-toast>
            <div class="toast-icon">{{ $toastType === 'success' ? '✓' : '!' }}</div>
            <div>
                <p class="toast-title">{{ $toastType === 'success' ? 'Berhasil' : 'Gagal' }}</p>
                <p class="toast-message">{{ $toastMessage }}</p>
            </div>
            <button type="button" data-toast-close aria-label="Tutup notifikasi">×</button>
        </div>
    @endif

    <div class="master-page">
        <div class="master-toolbar">
            <form id="lokasi-search-form" class="flex flex-wrap gap-2" method="GET" action="{{ route('admin.locations.index') }}">
                <input
                    id="lokasi-search-input"
                    class="master-search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari lokasi atau status..."
                    autocomplete="off">
                <select id="lokasi-status-filter" class="form-select min-w-44" name="status_kerawanan">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $status)
                        <option value="{{ $status }}" @selected(request('status_kerawanan') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </form>
            <button class="btn-primary" type="button" data-modal-target="create-lokasi-modal">Tambah Lokasi</button>
        </div>

        <div id="lokasi-results">
            <table class="master-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Lokasi / Area Pantau</th>
                        <th>Koordinat Pusat</th>
                        <th>Status Kerawanan</th>
                        <th>Polygon</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $items->firstItem() + $loop->index }}</td>
                            <td class="font-semibold">{{ $item->nama_lokasi }}</td>
                            <td>{{ $item->latitude ?? '-' }}, {{ $item->longitude ?? '-' }}</td>
                            <td>
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $statusBadgeClass[$item->status_kerawanan] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $item->status_kerawanan }}
                                </span>
                            </td>
                            <td>{{ $item->polygon_geojson ? 'Tersedia' : '-' }}</td>
                            <td class="space-x-1 whitespace-nowrap">
                                <button class="btn-edit" type="button" data-modal-target="edit-lokasi-modal-{{ $item->id }}">✎</button>
                                <button class="btn-delete" type="button" data-modal-target="delete-lokasi-modal-{{ $item->id }}">×</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500">Data lokasi belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">{{ $items->links() }}</div>

            <div id="create-lokasi-modal" class="modal-backdrop" hidden>
                <div class="modal-card">
                    <div class="modal-header">
                        <h2>Tambah Lokasi / Area Pantau</h2>
                        <button type="button" data-modal-close="create-lokasi-modal">×</button>
                    </div>
                    <form class="modal-body space-y-4" method="POST" action="{{ route('admin.locations.store') }}">
                        @csrf
                        @include('admin.master.lokasi.partials.fields', ['item' => null])
                        <div class="modal-footer">
                            <button class="btn-secondary" type="button" data-modal-close="create-lokasi-modal">Batal</button>
                            <button class="btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($items as $item)
                <div id="edit-lokasi-modal-{{ $item->id }}" class="modal-backdrop" hidden>
                    <div class="modal-card">
                        <div class="modal-header">
                            <h2>Edit Lokasi / Area Pantau</h2>
                            <button type="button" data-modal-close="edit-lokasi-modal-{{ $item->id }}">×</button>
                        </div>
                        <form class="modal-body space-y-4" method="POST" action="{{ route('admin.locations.update', $item) }}">
                            @csrf
                            @method('PUT')
                            @include('admin.master.lokasi.partials.fields', ['item' => $item])
                            <div class="modal-footer">
                                <button class="btn-secondary" type="button" data-modal-close="edit-lokasi-modal-{{ $item->id }}">Batal</button>
                                <button class="btn-primary" type="submit">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-lokasi-modal-{{ $item->id }}" class="modal-backdrop" hidden>
                    <div class="modal-card modal-card-sm">
                        <div class="modal-header">
                            <h2>Hapus Lokasi</h2>
                            <button type="button" data-modal-close="delete-lokasi-modal-{{ $item->id }}">×</button>
                        </div>
                        <div class="modal-body">
                            <p class="text-sm text-gray-600">Yakin ingin menghapus <strong>{{ $item->nama_lokasi }}</strong>?</p>
                            <form class="modal-footer" method="POST" action="{{ route('admin.locations.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-secondary" type="button" data-modal-close="delete-lokasi-modal-{{ $item->id }}">Batal</button>
                                <button class="btn-delete-text" type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function resetAndCloseModal(modal) {
            if (!modal) return;

            modal.querySelectorAll('form').forEach((form) => form.reset());
            modal.setAttribute('hidden', true);
        }

        const lokasiSearchForm = document.getElementById('lokasi-search-form');
        const lokasiSearchInput = document.getElementById('lokasi-search-input');
        const lokasiStatusFilter = document.getElementById('lokasi-status-filter');
        let lokasiSearchTimer;
        let lokasiSearchController;

        async function loadLokasiUrl(url, pushState = true) {
            lokasiSearchController?.abort();
            lokasiSearchController = new AbortController();

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: lokasiSearchController.signal,
            });

            const html = await response.text();
            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextResults = nextDocument.getElementById('lokasi-results');
            const currentResults = document.getElementById('lokasi-results');

            if (nextResults && currentResults) {
                currentResults.innerHTML = nextResults.innerHTML;
            }

            if (pushState) {
                window.history.replaceState({}, '', url);
            }
        }

        function buildLokasiUrl() {
            const url = new URL(lokasiSearchForm.action, window.location.origin);
            const searchValue = lokasiSearchInput.value.trim();
            const statusValue = lokasiStatusFilter.value;

            if (searchValue) {
                url.searchParams.set('search', searchValue);
            }

            if (statusValue) {
                url.searchParams.set('status_kerawanan', statusValue);
            }

            return url.toString();
        }

        lokasiSearchForm?.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        lokasiSearchInput?.addEventListener('input', function () {
            clearTimeout(lokasiSearchTimer);

            lokasiSearchTimer = setTimeout(() => {
                loadLokasiUrl(buildLokasiUrl()).catch((error) => {
                    if (error.name !== 'AbortError') console.error(error);
                });
            }, 300);
        });

        lokasiStatusFilter?.addEventListener('change', function () {
            loadLokasiUrl(buildLokasiUrl()).catch((error) => {
                if (error.name !== 'AbortError') console.error(error);
            });
        });

        document.addEventListener('click', function (event) {
            const paginationLink = event.target.closest('#lokasi-results nav a');
            if (paginationLink) {
                event.preventDefault();
                loadLokasiUrl(paginationLink.href).catch((error) => {
                    if (error.name !== 'AbortError') console.error(error);
                });
                return;
            }

            const targetId = event.target.closest('[data-modal-target]')?.dataset.modalTarget;
            if (targetId) {
                document.getElementById(targetId)?.removeAttribute('hidden');
            }

            const closeId = event.target.closest('[data-modal-close]')?.dataset.modalClose;
            if (closeId) {
                resetAndCloseModal(document.getElementById(closeId));
            }

            if (event.target.classList.contains('modal-backdrop')) {
                resetAndCloseModal(event.target);
            }

            if (event.target.closest('[data-toast-close]')) {
                event.target.closest('[data-toast]')?.remove();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop:not([hidden])').forEach(resetAndCloseModal);
            }
        });

        document.querySelectorAll('[data-toast]').forEach((toast) => {
            setTimeout(() => toast.remove(), 4500);
        });
    </script>
</x-admin-layout>
