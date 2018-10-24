<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarcasController extends Controller
{
    public function getMarcas(Request $request) {
        return DB::select("SELECT * FROM marcas");
    }

    public function getModelos(Request $request, $id) {
        return DB::select("SELECT * FROM modelos WHERE id_marca = " . $id . " ORDER BY nome ASC");
    }
}
