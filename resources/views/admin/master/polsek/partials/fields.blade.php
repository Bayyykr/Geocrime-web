@php
    $polsek = $item;
@endphp

<div>
    <label class="form-label">Nama Polsek</label>
    <input class="form-input" name="nama" value="{{ $polsek?->nama }}" placeholder="Contoh: Polsek Lumajang" required>
</div>

<div>
    <label class="form-label">Wilayah Master Lokasi</label>
    <select class="form-select" name="lokasi_id">
        <option value="">Pilih wilayah lokasi</option>
        @foreach (($locations ?? collect()) as $location)
            <option value="{{ $location->id }}" @selected((string) $polsek?->lokasi_id === (string) $location->id)>{{ $location->nama_lokasi }}</option>
        @endforeach
    </select>
</div>

<div>
    <label class="form-label">Alamat</label>
    <textarea class="form-input min-h-[90px]" name="alamat" placeholder="Alamat polsek">{{ $polsek?->alamat }}</textarea>
</div>

<div>
    <label class="form-label">Telepon</label>
    <input class="form-input" name="telepon" value="{{ $polsek?->telepon }}" placeholder="Contoh: 0334xxxxxx">
</div>
