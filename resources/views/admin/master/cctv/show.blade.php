<x-admin-layout>
    <x-slot name="header">Live CCTV</x-slot>

    <div class="master-page">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $item->nama }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $item->url_stream ?: 'URL live streaming belum diisi.' }}</p>
                <p class="mt-1 text-sm text-gray-500">Wilayah: {{ $item->lokasi?->nama_lokasi ?? '-' }}</p>
            </div>
            <a class="btn-secondary" href="{{ route('admin.cctvs.index') }}">Kembali</a>
        </div>

        @if ($item->embed_url)
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-black shadow-sm">
                <div class="aspect-video w-full">
                    <iframe
                        class="h-full w-full"
                        src="{{ $item->embed_url }}"
                        title="Live Streaming {{ $item->nama }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
        @else
            <div class="rounded border border-yellow-200 bg-yellow-50 p-5 text-yellow-800">
                URL live streaming belum tersedia. Silakan edit data CCTV dan masukkan URL livestream YouTube.
            </div>
        @endif
    </div>
</x-admin-layout>
