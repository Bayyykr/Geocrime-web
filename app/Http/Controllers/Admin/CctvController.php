<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CctvController extends Controller
{
    public function index(Request $request): View
    {
        $items = Cctv::query()
            ->when(
                $request->search,
                fn($query, $search) => $query->where(
                    "nama",
                    "like",
                    "%{$search}%",
                ),
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view("admin.master.cctv.index", compact("items"));
    }

    public function create(): View
    {
        return view("admin.master.cctv.form", [
            "item" => new Cctv(["aktif" => true]),
            "locations" => Location::query()->orderBy("nama_lokasi")->get(),
        ]);
    }

    public function show(Cctv $cctv): View
    {
        return view("admin.master.cctv.show", [
            "item" => $cctv->load("lokasi"),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Cctv::create($this->validated($request));

        return redirect()
            ->route("admin.cctvs.index")
            ->with("success", "CCTV berhasil ditambahkan.");
    }

    public function edit(Cctv $cctv): View
    {
        return view("admin.master.cctv.form", [
            "item" => $cctv,
            "locations" => Location::query()->orderBy("nama_lokasi")->get(),
        ]);
    }

    public function update(Request $request, Cctv $cctv): RedirectResponse
    {
        $cctv->update($this->validated($request));

        return redirect()
            ->route("admin.cctvs.index")
            ->with("success", "CCTV berhasil diperbarui.");
    }

    public function destroy(Cctv $cctv): RedirectResponse
    {
        $cctv->delete();

        return redirect()
            ->route("admin.cctvs.index")
            ->with("success", "CCTV berhasil dihapus.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            "lokasi_id" => ["nullable", "exists:locations,id"],
            "nama" => ["required", "string", "max:255"],
            "url_stream" => ["nullable", "url", "max:255"],
            "latitude" => ["nullable", "numeric", "between:-90,90"],
            "longitude" => ["nullable", "numeric", "between:-180,180"],
            "aktif" => ["nullable", "boolean"],
        ]);

        $data["aktif"] = $request->boolean("aktif");

        return $data;
    }
}
