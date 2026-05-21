<?php

namespace Database\Seeders;

use App\Models\Polsek;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                "name" => "Super Admin",
                "username" => "superadmin",
                "email" => "admin@geocrime.test",
                "telepon" => "081234560001",
                "alamat" => "Kantor Polres Lumajang",
                "role" => "admin",
                "aktif" => true,
            ],
            [
                "name" => "Operator CCTV",
                "username" => "operator.cctv",
                "email" => "operator.cctv@geocrime.test",
                "telepon" => "081234560002",
                "alamat" => "Ruang Command Center Lumajang",
                "role" => "operator",
                "aktif" => true,
            ],
            [
                "name" => "Admin Polsek Lumajang",
                "username" => "admin.lumajang",
                "email" => "admin.lumajang@geocrime.test",
                "telepon" => "081234560003",
                "alamat" => "Jl. Alun-Alun Lumajang",
                "role" => "admin",
                "aktif" => true,
            ],
            [
                "name" => "Rina Anggraini",
                "username" => "rina.anggraini",
                "email" => "rina@geocrime.test",
                "telepon" => "081234560004",
                "alamat" => "Kecamatan Sukodono",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Budi Santoso",
                "username" => "budi.santoso",
                "email" => "budi@geocrime.test",
                "telepon" => "081234560005",
                "alamat" => "Kecamatan Sumbersuko",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Dewi Lestari",
                "username" => "dewi.lestari",
                "email" => "dewi@geocrime.test",
                "telepon" => "081234560006",
                "alamat" => "Kecamatan Tekung",
                "role" => "operator",
                "aktif" => true,
            ],
            [
                "name" => "Agus Prasetyo",
                "username" => "agus.prasetyo",
                "email" => "agus@geocrime.test",
                "telepon" => "081234560007",
                "alamat" => "Kecamatan Senduro",
                "role" => "user",
                "aktif" => false,
            ],
            [
                "name" => "Siti Aminah",
                "username" => "siti.aminah",
                "email" => "siti@geocrime.test",
                "telepon" => "081234560008",
                "alamat" => "Kecamatan Pasirian",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Eko Wahyudi",
                "username" => "eko.wahyudi",
                "email" => "eko@geocrime.test",
                "telepon" => "081234560009",
                "alamat" => "Kecamatan Klakah",
                "role" => "operator",
                "aktif" => true,
            ],
            [
                "name" => "Maya Putri",
                "username" => "maya.putri",
                "email" => "maya@geocrime.test",
                "telepon" => "081234560010",
                "alamat" => "Kecamatan Candipuro",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Andi Saputra",
                "username" => "andi.saputra",
                "email" => "andi@geocrime.test",
                "telepon" => "081234560011",
                "alamat" => "Kecamatan Yosowilangun",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Fitri Handayani",
                "username" => "fitri.handayani",
                "email" => "fitri@geocrime.test",
                "telepon" => "081234560012",
                "alamat" => "Kecamatan Randuagung",
                "role" => "admin",
                "aktif" => true,
            ],
            [
                "name" => "Hendra Wijaya",
                "username" => "hendra.wijaya",
                "email" => "hendra@geocrime.test",
                "telepon" => "081234560013",
                "alamat" => "Kecamatan Jatiroto",
                "role" => "operator",
                "aktif" => false,
            ],
            [
                "name" => "Nia Kurniawati",
                "username" => "nia.kurniawati",
                "email" => "nia@geocrime.test",
                "telepon" => "081234560014",
                "alamat" => "Kecamatan Rowokangkung",
                "role" => "user",
                "aktif" => true,
            ],
            [
                "name" => "Yoga Firmansyah",
                "username" => "yoga.firmansyah",
                "email" => "yoga@geocrime.test",
                "telepon" => "081234560015",
                "alamat" => "Kecamatan Tempursari",
                "role" => "user",
                "aktif" => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ["email" => $user["email"]],
                $user + ["password" => Hash::make("password")],
            );
        }

        $polseks = [
            [
                "nama" => "Polsek Lumajang Kota",
                "wilayah" => "Kecamatan Lumajang",
                "alamat" => "Jl. Alun-Alun Barat, Lumajang",
                "telepon" => "0334-881001",
            ],
            [
                "nama" => "Polsek Sukodono",
                "wilayah" => "Kecamatan Sukodono",
                "alamat" => "Jl. Raya Sukodono, Lumajang",
                "telepon" => "0334-881002",
            ],
            [
                "nama" => "Polsek Sumbersuko",
                "wilayah" => "Kecamatan Sumbersuko",
                "alamat" => "Jl. Raya Sumbersuko, Lumajang",
                "telepon" => "0334-881003",
            ],
            [
                "nama" => "Polsek Tekung",
                "wilayah" => "Kecamatan Tekung",
                "alamat" => "Jl. Raya Tekung, Lumajang",
                "telepon" => "0334-881004",
            ],
            [
                "nama" => "Polsek Senduro",
                "wilayah" => "Kecamatan Senduro",
                "alamat" => "Jl. Raya Senduro, Lumajang",
                "telepon" => "0334-881005",
            ],
            [
                "nama" => "Polsek Pasirian",
                "wilayah" => "Kecamatan Pasirian",
                "alamat" => "Jl. Raya Pasirian, Lumajang",
                "telepon" => "0334-881006",
            ],
            [
                "nama" => "Polsek Candipuro",
                "wilayah" => "Kecamatan Candipuro",
                "alamat" => "Jl. Raya Candipuro, Lumajang",
                "telepon" => "0334-881007",
            ],
            [
                "nama" => "Polsek Klakah",
                "wilayah" => "Kecamatan Klakah",
                "alamat" => "Jl. Raya Klakah, Lumajang",
                "telepon" => "0334-881008",
            ],
            [
                "nama" => "Polsek Ranuyoso",
                "wilayah" => "Kecamatan Ranuyoso",
                "alamat" => "Jl. Raya Ranuyoso, Lumajang",
                "telepon" => "0334-881009",
            ],
            [
                "nama" => "Polsek Jatiroto",
                "wilayah" => "Kecamatan Jatiroto",
                "alamat" => "Jl. Raya Jatiroto, Lumajang",
                "telepon" => "0334-881010",
            ],
            [
                "nama" => "Polsek Randuagung",
                "wilayah" => "Kecamatan Randuagung",
                "alamat" => "Jl. Raya Randuagung, Lumajang",
                "telepon" => "0334-881011",
            ],
            [
                "nama" => "Polsek Yosowilangun",
                "wilayah" => "Kecamatan Yosowilangun",
                "alamat" => "Jl. Raya Yosowilangun, Lumajang",
                "telepon" => "0334-881012",
            ],
            [
                "nama" => "Polsek Rowokangkung",
                "wilayah" => "Kecamatan Rowokangkung",
                "alamat" => "Jl. Raya Rowokangkung, Lumajang",
                "telepon" => "0334-881013",
            ],
            [
                "nama" => "Polsek Tempursari",
                "wilayah" => "Kecamatan Tempursari",
                "alamat" => "Jl. Raya Tempursari, Lumajang",
                "telepon" => "0334-881014",
            ],
            [
                "nama" => "Polsek Pronojiwo",
                "wilayah" => "Kecamatan Pronojiwo",
                "alamat" => "Jl. Raya Pronojiwo, Lumajang",
                "telepon" => "0334-881015",
            ],
        ];

        foreach ($polseks as $polsek) {
            Polsek::updateOrCreate(["nama" => $polsek["nama"]], $polsek);
        }
    }
}
