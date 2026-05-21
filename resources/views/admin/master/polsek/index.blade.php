<x-admin-layout>
    <x-slot name="header">Polsek</x-slot>

    @php
        $toastType = session('success') ? 'success' : (session('error') || $errors->any() ? 'error' : null);
        $toastMessage = session('success') ?: session('error') ?: ($errors->any() ? $errors->first() : null);
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
            <form id="polsek-search-form" method="GET" action="{{ route('admin.polseks.index') }}">
                <input
                    id="polsek-search-input"
                    class="master-search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari polsek..."
                    autocomplete="off">
            </form>
            <button class="btn-primary" type="button" data-modal-target="create-polsek-modal">Tambah Polsek</button>
        </div>

        <div id="polsek-results">
            <table class="master-table">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Polsek</th>
                        <th>Wilayah/Kecamatan</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $items->firstItem() + $loop->index }}</td>
                            <td class="font-semibold">{{ $item->nama }}</td>
                            <td>{{ $item->wilayah ?? '-' }}</td>
                            <td>{{ $item->alamat ?? '-' }}</td>
                            <td>{{ $item->telepon ?? '-' }}</td>
                            <td class="space-x-1 whitespace-nowrap">
                                <button class="btn-edit" type="button" data-modal-target="edit-polsek-modal-{{ $item->id }}">✎</button>
                                <button class="btn-delete" type="button" data-modal-target="delete-polsek-modal-{{ $item->id }}">×</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-gray-500">Data polsek belum tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">{{ $items->links() }}</div>

            <div id="create-polsek-modal" class="modal-backdrop" hidden>
                <div class="modal-card modal-card-sm">
                    <div class="modal-header">
                        <h2>Tambah Polsek</h2>
                        <button type="button" data-modal-close="create-polsek-modal">×</button>
                    </div>
                    <form class="modal-body space-y-4" method="POST" action="{{ route('admin.polseks.store') }}">
                        @csrf
                        @include('admin.master.polsek.partials.fields', ['item' => null])
                        <div class="modal-footer">
                            <button class="btn-secondary" type="button" data-modal-close="create-polsek-modal">Batal</button>
                            <button class="btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($items as $item)
                <div id="edit-polsek-modal-{{ $item->id }}" class="modal-backdrop" hidden>
                    <div class="modal-card modal-card-sm">
                        <div class="modal-header">
                            <h2>Edit Polsek</h2>
                            <button type="button" data-modal-close="edit-polsek-modal-{{ $item->id }}">×</button>
                        </div>
                        <form class="modal-body space-y-4" method="POST" action="{{ route('admin.polseks.update', $item) }}">
                            @csrf
                            @method('PUT')
                            @include('admin.master.polsek.partials.fields', ['item' => $item])
                            <div class="modal-footer">
                                <button class="btn-secondary" type="button" data-modal-close="edit-polsek-modal-{{ $item->id }}">Batal</button>
                                <button class="btn-primary" type="submit">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="delete-polsek-modal-{{ $item->id }}" class="modal-backdrop" hidden>
                    <div class="modal-card modal-card-sm">
                        <div class="modal-header">
                            <h2>Hapus Polsek</h2>
                            <button type="button" data-modal-close="delete-polsek-modal-{{ $item->id }}">×</button>
                        </div>
                        <div class="modal-body">
                            <p class="text-sm text-gray-600">Yakin ingin menghapus <strong>{{ $item->nama }}</strong>?</p>
                            <form class="modal-footer" method="POST" action="{{ route('admin.polseks.destroy', $item) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn-secondary" type="button" data-modal-close="delete-polsek-modal-{{ $item->id }}">Batal</button>
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

        const polsekSearchForm = document.getElementById('polsek-search-form');
        const polsekSearchInput = document.getElementById('polsek-search-input');
        let polsekSearchTimer;
        let polsekSearchController;

        async function loadPolsekUrl(url, pushState = true) {
            polsekSearchController?.abort();
            polsekSearchController = new AbortController();

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: polsekSearchController.signal,
            });

            const html = await response.text();
            const nextDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextResults = nextDocument.getElementById('polsek-results');
            const currentResults = document.getElementById('polsek-results');

            if (nextResults && currentResults) {
                currentResults.innerHTML = nextResults.innerHTML;
            }

            if (pushState) {
                window.history.replaceState({}, '', url);
            }
        }

        polsekSearchForm?.addEventListener('submit', function (event) {
            event.preventDefault();
        });

        polsekSearchInput?.addEventListener('input', function () {
            clearTimeout(polsekSearchTimer);

            polsekSearchTimer = setTimeout(() => {
                const url = new URL(polsekSearchForm.action, window.location.origin);
                const searchValue = polsekSearchInput.value.trim();

                if (searchValue) {
                    url.searchParams.set('search', searchValue);
                }

                loadPolsekUrl(url.toString()).catch((error) => {
                    if (error.name !== 'AbortError') console.error(error);
                });
            }, 300);
        });

        document.addEventListener('click', function (event) {
            const paginationLink = event.target.closest('#polsek-results nav a');
            if (paginationLink) {
                event.preventDefault();
                loadPolsekUrl(paginationLink.href).catch((error) => {
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
