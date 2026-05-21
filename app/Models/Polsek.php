<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(["nama", "wilayah", "alamat", "telepon"])]
class Polsek extends Model {}
