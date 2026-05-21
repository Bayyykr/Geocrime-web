<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $items = Location::query()
            ->when(
                $request->search,
                fn($query, $search) => $query->where(function ($query) use (
                    $search,
                ) {
                    $query
                        ->where("nama_lokasi", "like", "%{$search}%")
                        ->orWhere("status_kerawanan", "like", "%{$search}%");
                }),
            )
            ->when(
                $request->status_kerawanan,
                fn($query, $status) => $query->where(
                    "status_kerawanan",
                    $status,
                ),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("admin.master.lokasi.index", compact("items"));
    }

    public function store(Request $request): RedirectResponse
    {
        Location::create($this->validated($request));

        return redirect()
            ->route("admin.locations.index")
            ->with("success", "Lokasi berhasil ditambahkan.");
    }

    public function update(
        Request $request,
        Location $location,
    ): RedirectResponse {
        $location->update($this->validated($request));

        return redirect()
            ->route("admin.locations.index")
            ->with("success", "Lokasi berhasil diperbarui.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()
            ->route("admin.locations.index")
            ->with("success", "Lokasi berhasil dihapus.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate(
            [
                "nama_lokasi" => ["required", "string", "max:255"],
                "latitude" => ["nullable", "numeric", "between:-90,90"],
                "longitude" => ["nullable", "numeric", "between:-180,180"],
                "polygon_geojson" => ["nullable", "json"],
                "status_kerawanan" => [
                    "required",
                    Rule::in(["Aman", "Rawan", "Sangat Rawan"]),
                ],
            ],
            [
                "polygon_geojson.json" =>
                    "Polygon GeoJSON harus berupa format JSON yang valid.",
            ],
        );

        $data["polygon_geojson"] = $this->normalizeGeojson(
            $data["polygon_geojson"] ?? null,
        );

        return $data;
    }

    private function normalizeGeojson(?string $geojson): ?string
    {
        if ($geojson === null || trim($geojson) === "") {
            return null;
        }

        $decoded = json_decode($geojson, true);

        if (!is_array($decoded) || !$this->isSupportedGeojson($decoded)) {
            throw ValidationException::withMessages([
                "polygon_geojson" =>
                    "Polygon GeoJSON harus berisi objek GeoJSON valid dengan type Polygon, MultiPolygon, Feature, atau FeatureCollection.",
            ]);
        }

        return json_encode(
            $decoded,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }

    private function isSupportedGeojson(array $geojson): bool
    {
        $type = $geojson["type"] ?? null;

        if ($type === "Polygon" || $type === "MultiPolygon") {
            return !empty($geojson["coordinates"]) &&
                is_array($geojson["coordinates"]);
        }

        if ($type === "Feature") {
            return isset($geojson["geometry"]) &&
                is_array($geojson["geometry"]) &&
                $this->isSupportedGeojson($geojson["geometry"]);
        }

        if ($type === "FeatureCollection") {
            if (
                empty($geojson["features"]) ||
                !is_array($geojson["features"])
            ) {
                return false;
            }

            foreach ($geojson["features"] as $feature) {
                if (
                    !is_array($feature) ||
                    !$this->isSupportedGeojson($feature)
                ) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
