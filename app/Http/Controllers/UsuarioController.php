<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UsuarioController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT `name`, `email` FROM `users`");
    }
}
