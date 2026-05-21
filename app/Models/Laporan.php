<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[
    Fillable([
        "user_id",
        "kategori_id",
        "lokasi_id",
        "polsek_id",
        "judul_laporan",
        "deskripsi",
        "latitude",
        "longitude",
        "foto_kejadian",
        "status",
    ]),
]
class Laporan extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Category::class, "kategori_id");
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Location::class, "lokasi_id");
    }

    public function polsek(): BelongsTo
    {
        return $this->belongsTo(Polsek::class);
    }

    public function konfirmasi(): HasOne
    {
        return $this->hasOne("App\\Models\\KonfirmasiLaporan");
    }
}
