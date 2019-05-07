<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ParticipanteController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT * FROM `users` WHERE `id` != 1 AND `ativo` = 1 ORDER BY `name` ASC");
    }

    public function getParticipante(Request $request, $id) {
        try {
           return DB::select("SELECT * FROM `users` WHERE `ativo` = 1 AND id = ?", [$id]);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }
}
