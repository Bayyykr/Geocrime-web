<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Polsek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolsekController extends Controller
{
    public function index(Request $request): View
    {
        $items = Polsek::query()
            ->when(
                $request->search,
                fn($query, $search) => $query->where(function ($query) use (
                    $search,
                ) {
                    $query
                        ->where("nama", "like", "%{$search}%")
                        ->orWhere("wilayah", "like", "%{$search}%")
                        ->orWhere("alamat", "like", "%{$search}%")
                        ->orWhere("telepon", "like", "%{$search}%");
                }),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("admin.master.polsek.index", compact("items"));
    }

    public function store(Request $request): RedirectResponse
    {
        Polsek::create($this->validated($request));

        return redirect()
            ->route("admin.polseks.index")
            ->with("success", "Polsek berhasil ditambahkan.");
    }

    public function update(Request $request, Polsek $polsek): RedirectResponse
    {
        $polsek->update($this->validated($request));

        return redirect()
            ->route("admin.polseks.index")
            ->with("success", "Polsek berhasil diperbarui.");
    }

    public function destroy(Polsek $polsek): RedirectResponse
    {
        $polsek->delete();

        return redirect()
            ->route("admin.polseks.index")
            ->with("success", "Polsek berhasil dihapus.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            "nama" => ["required", "string", "max:255"],
            "wilayah" => ["nullable", "string", "max:100"],
            "alamat" => ["nullable", "string", "max:255"],
            "telepon" => ["nullable", "string", "max:30"],
        ]);
    }
}
