<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EmergencyReport;
use App\Models\Laporan;
use App\Models\Polsek;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function daruratStatus(): JsonResponse
    {
        return response()->json([
            "active" => EmergencyReport::where("status", "aktif")->count(),
        ]);
    }

    public function infografik(Request $request): View
    {
        $selectedMonth = $request->input("bulan", now()->format("Y-m"));

        try {
            $selectedMonthDate = Carbon::createFromFormat(
                "Y-m",
                $selectedMonth,
            )->startOfMonth();
        } catch (\Throwable) {
            $selectedMonthDate = now()->startOfMonth();
            $selectedMonth = $selectedMonthDate->format("Y-m");
        }

        $year = $selectedMonthDate->year;
        $startDate = $request->filled("tanggal_mulai")
            ? Carbon::parse($request->tanggal_mulai)->startOfDay()
            : $selectedMonthDate->copy()->startOfMonth();
        $endDate = $request->filled("tanggal_selesai")
            ? Carbon::parse($request->tanggal_selesai)->endOfDay()
            : $selectedMonthDate->copy()->endOfMonth();

        if ($endDate->lt($startDate)) {
            [$startDate, $endDate] = [
                $endDate->copy()->startOfDay(),
                $startDate->copy()->endOfDay(),
            ];
        }

        $monthlyCrime = array_fill(0, 12, 0);
        $monthlyAccident = array_fill(0, 12, 0);

        Laporan::query()
            ->join("categories", "categories.id", "=", "laporans.kategori_id")
            ->selectRaw(
                "MONTH(laporans.created_at) as month_number, categories.jenis, COUNT(*) as total",
            )
            ->whereYear("laporans.created_at", $year)
            ->groupBy("month_number", "categories.jenis")
            ->get()
            ->each(function ($row) use (&$monthlyCrime, &$monthlyAccident) {
                $index = ((int) $row->month_number) - 1;

                if ($row->jenis === "kejahatan") {
                    $monthlyCrime[$index] = (int) $row->total;
                }

                if ($row->jenis === "kecelakaan") {
                    $monthlyAccident[$index] = (int) $row->total;
                }
            });

        $locationRows = \App\Models\Location::query()
            ->select("locations.id", "locations.nama_lokasi")
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query
                    ->from("laporans")
                    ->join(
                        "categories",
                        "categories.id",
                        "=",
                        "laporans.kategori_id",
                    )
                    ->selectRaw("COUNT(*)")
                    ->whereColumn("laporans.lokasi_id", "locations.id")
                    ->where("categories.jenis", "kejahatan")
                    ->whereBetween("laporans.created_at", [
                        $startDate,
                        $endDate,
                    ]);
            }, "kejahatan_total")
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query
                    ->from("laporans")
                    ->join(
                        "categories",
                        "categories.id",
                        "=",
                        "laporans.kategori_id",
                    )
                    ->selectRaw("COUNT(*)")
                    ->whereColumn("laporans.lokasi_id", "locations.id")
                    ->where("categories.jenis", "kecelakaan")
                    ->whereBetween("laporans.created_at", [
                        $startDate,
                        $endDate,
                    ]);
            }, "kecelakaan_total")
            ->orderBy("nama_lokasi")
            ->get();

        $categoryRows = Category::query()
            ->select(
                "categories.id",
                "categories.nama_kategori",
                "categories.jenis",
                "categories.warna_marker",
            )
            ->selectSub(function ($query) use ($startDate, $endDate) {
                $query
                    ->from("laporans")
                    ->selectRaw("COUNT(*)")
                    ->whereColumn("laporans.kategori_id", "categories.id")
                    ->whereBetween("laporans.created_at", [
                        $startDate,
                        $endDate,
                    ]);
            }, "laporan_total")
            ->orderByDesc("laporan_total")
            ->orderBy("nama_kategori")
            ->get();

        return view("admin.laporan.infografik", [
            "selectedMonth" => $selectedMonth,
            "startDate" => $startDate->toDateString(),
            "endDate" => $endDate->toDateString(),
            "chartData" => [
                "monthly" => [
                    "labels" => [
                        "Jan",
                        "Feb",
                        "Mar",
                        "Apr",
                        "May",
                        "Jun",
                        "Jul",
                        "Aug",
                        "Sep",
                        "Oct",
                        "Nov",
                        "Dec",
                    ],
                    "kejahatan" => $monthlyCrime,
                    "kecelakaan" => $monthlyAccident,
                ],
                "locations" => [
                    "labels" => $locationRows->pluck("nama_lokasi")->values(),
                    "kejahatan" => $locationRows
                        ->pluck("kejahatan_total")
                        ->map(fn($value) => (int) $value)
                        ->values(),
                    "kecelakaan" => $locationRows
                        ->pluck("kecelakaan_total")
                        ->map(fn($value) => (int) $value)
                        ->values(),
                ],
                "categories" => [
                    "labels" => $categoryRows->pluck("nama_kategori")->values(),
                    "totals" => $categoryRows
                        ->pluck("laporan_total")
                        ->map(fn($value) => (int) $value)
                        ->values(),
                    "colors" => $categoryRows
                        ->map(
                            fn($category) => $category->warna_marker ?:
                            ($category->jenis === "kecelakaan"
                                ? "#45b8a9"
                                : "#248cc6"),
                        )
                        ->values(),
                ],
            ],
        ]);
    }

    public function riwayat(Request $request): View
    {
        $query = $this->baseQuery($request)->where("status", "selesai");

        $items = $query->latest()->paginate(10)->withQueryString();
        $emergencyArchives = EmergencyReport::query()
            ->with(["user", "nearestPolsek.lokasi"])
            ->whereIn("status", ["selesai", "arsip"])
            ->latest("waktu_selesai")
            ->limit(10)
            ->get();

        return view("admin.laporan.riwayat", [
            "items" => $items,
            "emergencyArchives" => $emergencyArchives,
            "categories" => Category::orderBy("jenis")
                ->orderBy("nama_kategori")
                ->get(),
            "summary" => $this->summaryCards(),
        ]);
    }

    public function darurat(Request $request): View
    {
        $items = EmergencyReport::query()
            ->with(["user", "nearestPolsek.lokasi"])
            ->whereIn("status", ["aktif", "dalam_penanganan"])
            ->when($request->search, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where("kode_darurat", "like", "%{$search}%")
                        ->orWhere("alamat_terdeteksi", "like", "%{$search}%")
                        ->orWhereHas(
                            "user",
                            fn(Builder $query) => $query
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("telepon", "like", "%{$search}%"),
                        )
                        ->orWhereHas(
                            "nearestPolsek",
                            fn(Builder $query) => $query->where(
                                "nama",
                                "like",
                                "%{$search}%",
                            ),
                        );
                });
            })
            ->when(
                $request->status,
                fn(Builder $query, string $status) => $query->where(
                    "status",
                    $status,
                ),
            )
            ->latest("waktu_sos")
            ->paginate(8)
            ->withQueryString();

        return view("admin.laporan.darurat", [
            "items" => $items,
            "summary" => [
                "active" => EmergencyReport::where("status", "aktif")->count(),
                "handling" => EmergencyReport::where(
                    "status",
                    "dalam_penanganan",
                )->count(),
                "today" => EmergencyReport::whereDate(
                    "waktu_sos",
                    today(),
                )->count(),
                "done" => EmergencyReport::whereIn("status", [
                    "selesai",
                    "arsip",
                ])->count(),
            ],
        ]);
    }

    public function dispatchDarurat(
        EmergencyReport $emergencyReport,
    ): RedirectResponse {
        if ($emergencyReport->status === "aktif") {
            $emergencyReport->update([
                "status" => "dalam_penanganan",
                "waktu_dispatch" => now(),
                "petugas_penanganan" => Auth::user()?->name,
            ]);
        }

        return redirect()
            ->route("admin.laporan.darurat")
            ->with("success", "Personel berhasil dikirim ke titik darurat.");
    }

    public function completeDarurat(
        Request $request,
        EmergencyReport $emergencyReport,
    ): RedirectResponse {
        $data = $request->validate([
            "catatan_petugas" => ["required", "string", "max:1500"],
        ]);

        $emergencyReport->update([
            "status" => "selesai",
            "waktu_selesai" => now(),
            "petugas_penanganan" =>
                $emergencyReport->petugas_penanganan ?: Auth::user()?->name,
            "catatan_petugas" => $data["catatan_petugas"],
        ]);

        return redirect()
            ->route("admin.laporan.riwayat")
            ->with(
                "success",
                "Laporan darurat selesai dan masuk ke riwayat laporan.",
            );
    }

    public function storeDarurat(Request $request): RedirectResponse
    {
        $data = $request->validate([
            "latitude" => ["required", "numeric", "between:-90,90"],
            "longitude" => ["required", "numeric", "between:-180,180"],
            "alamat_terdeteksi" => ["nullable", "string", "max:1000"],
        ]);

        $nearest = $this->nearestPolsek(
            (float) $data["latitude"],
            (float) $data["longitude"],
        );

        EmergencyReport::create([
            "user_id" => Auth::id(),
            "nearest_polsek_id" => $nearest["polsek"]?->id,
            "kode_darurat" => $this->generateEmergencyCode(),
            "status" => "aktif",
            "latitude" => $data["latitude"],
            "longitude" => $data["longitude"],
            "alamat_terdeteksi" => $data["alamat_terdeteksi"] ?? null,
            "jarak_polsek_km" => $nearest["distance"],
            "waktu_sos" => now(),
            "telemetri" => [
                [
                    "lat" => (float) $data["latitude"],
                    "lng" => (float) $data["longitude"],
                    "time" => now()->toIso8601String(),
                ],
            ],
        ]);

        return redirect()
            ->route("admin.laporan.darurat")
            ->with("success", "Simulasi SOS berhasil dibuat.");
    }

    private function baseQuery(Request $request): Builder
    {
        return Laporan::query()
            ->with([
                "user",
                "kategori",
                "lokasi",
                "polsek",
                "konfirmasi.petugas",
            ])
            ->when($request->search, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where("judul_laporan", "like", "%{$search}%")
                        ->orWhere("deskripsi", "like", "%{$search}%")
                        ->orWhereHas(
                            "user",
                            fn(Builder $query) => $query
                                ->where("name", "like", "%{$search}%")
                                ->orWhere("email", "like", "%{$search}%")
                                ->orWhere("telepon", "like", "%{$search}%"),
                        )
                        ->orWhereHas(
                            "kategori",
                            fn(Builder $query) => $query->where(
                                "nama_kategori",
                                "like",
                                "%{$search}%",
                            ),
                        )
                        ->orWhereHas(
                            "lokasi",
                            fn(Builder $query) => $query->where(
                                "nama_lokasi",
                                "like",
                                "%{$search}%",
                            ),
                        )
                        ->orWhereHas(
                            "polsek",
                            fn(Builder $query) => $query->where(
                                "nama",
                                "like",
                                "%{$search}%",
                            ),
                        );
                });
            })
            ->when(
                $request->status,
                fn(Builder $query, string $status) => $query->where(
                    "status",
                    $status,
                ),
            )
            ->when(
                $request->jenis,
                fn(Builder $query, string $jenis) => $query->whereHas(
                    "kategori",
                    fn(Builder $query) => $query->where("jenis", $jenis),
                ),
            )
            ->when(
                $request->kategori_id,
                fn(Builder $query, string $kategoriId) => $query->where(
                    "kategori_id",
                    $kategoriId,
                ),
            )
            ->when(
                $request->tanggal_mulai,
                fn(Builder $query, string $date) => $query->whereDate(
                    "created_at",
                    ">=",
                    $date,
                ),
            )
            ->when(
                $request->tanggal_selesai,
                fn(Builder $query, string $date) => $query->whereDate(
                    "created_at",
                    "<=",
                    $date,
                ),
            );
    }

    private function nearestPolsek(float $latitude, float $longitude): array
    {
        $nearest = null;
        $nearestDistance = null;

        Polsek::query()
            ->with("lokasi")
            ->get()
            ->each(function (Polsek $polsek) use (
                $latitude,
                $longitude,
                &$nearest,
                &$nearestDistance,
            ) {
                if (
                    !$polsek->lokasi?->latitude ||
                    !$polsek->lokasi?->longitude
                ) {
                    return;
                }

                $distance = $this->haversineDistance(
                    $latitude,
                    $longitude,
                    (float) $polsek->lokasi->latitude,
                    (float) $polsek->lokasi->longitude,
                );

                if ($nearestDistance === null || $distance < $nearestDistance) {
                    $nearest = $polsek;
                    $nearestDistance = $distance;
                }
            });

        return [
            "polsek" => $nearest,
            "distance" =>
                $nearestDistance !== null ? round($nearestDistance, 2) : null,
        ];
    }

    private function haversineDistance(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2,
    ): float {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a =
            sin($latDelta / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function generateEmergencyCode(): string
    {
        $nextNumber =
            EmergencyReport::whereDate("created_at", today())->count() + 1;

        return "SOS-" .
            now()->format("Ymd") .
            "-" .
            str_pad((string) $nextNumber, 3, "0", STR_PAD_LEFT);
    }

    private function summaryCards(): array
    {
        return [
            "total" =>
                Laporan::where("status", "selesai")->count() +
                EmergencyReport::whereIn("status", [
                    "selesai",
                    "arsip",
                ])->count(),
            "pending" => Laporan::where("status", "pending")->count(),
            "confirmed" => Laporan::where("status", "dikonfirmasi")->count(),
            "done" => Laporan::where("status", "selesai")->count(),
        ];
    }
}
