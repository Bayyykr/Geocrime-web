<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CctvController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\PolsekController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::middleware(["auth", "verified"])->group(function () {
    Route::get("/dashboard", function () {
        return view("admin.dashboard");
    })->name("dashboard");

    Route::prefix("admin")
        ->name("admin.")
        ->group(function () {
            Route::resource("users", AdminUserController::class)->only([
                "index",
                "store",
                "update",
                "destroy",
            ]);
            Route::resource("polseks", PolsekController::class)->only([
                "index",
                "store",
                "update",
                "destroy",
            ]);
            Route::resource("categories", CategoryController::class)->only([
                "index",
                "store",
                "update",
                "destroy",
            ]);
            Route::resource("cctvs", CctvController::class);
            Route::resource("locations", LocationController::class)->only([
                "index",
                "store",
                "update",
                "destroy",
            ]);
        });

    Route::get("/profile", [ProfileController::class, "edit"])->name(
        "profile.edit",
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update",
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy",
    );
});

require __DIR__ . "/auth.php";
