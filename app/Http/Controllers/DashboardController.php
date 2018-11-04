<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{
    public function get(Request $request) {
        $arrList = array();
        $usuarios = DB::select("SELECT count(*) AS `usuarios` FROM `users`")[0];
        $arrList['usuarios'] = $usuarios->usuarios;

        $palestrantes = DB::select("SELECT count(*) AS `palestrantes` FROM `palestrante` WHERE `ativo` = 1")[0];
        $arrList['palestrantes'] = $palestrantes->palestrantes;

        $oficinas = DB::select("SELECT count(*) AS `oficinas` FROM `programacao` WHERE `ativo` = 1")[0];
        $arrList['oficinas'] = $oficinas->oficinas;

        $perguntas = DB::select("SELECT count(*) AS `perguntas` FROM `pergunta` LEFT JOIN `resposta` 
            ON `pergunta`.`id` = `resposta`.`id_pergunta` WHERE `resposta`.`id_pergunta` IS NULL")[0];
        $arrList['perguntas'] = $perguntas->perguntas;

        return $arrList;
    }
}
