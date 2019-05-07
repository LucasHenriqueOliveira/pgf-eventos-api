<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AvisosController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT * FROM `avisos` WHERE `ativo` = 1 ORDER BY `id` DESC");
    }
}
