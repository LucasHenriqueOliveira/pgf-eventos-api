<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ManualController extends Controller
{
    public function save(Request $request) {
        try {
            DB::insert('INSERT INTO `manual` (`item`, `km`, `tempo`, `id_marca`, `id_modelo`) VALUES (?, ?, ?, ?, ?)', 
            [$request->item, $request->km, $request->meses, $request->selectedMarca, $request->selectedModelo]);
            return $this->successResponse();
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Ocorreu um erro na operação.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse() {
        return response()->json([
            'data' => 'Item inserido com sucesso.'
        ], Response::HTTP_OK);
    }

    public function get(Request $request) {
        return DB::select("SELECT `man`.`id`, `man`.`item`, `man`.`km`, `man`.`tempo`, `mod`.`nome` as `modelo`, `mar`.`nome` as `marca`, `mod`.`id` as `id_modelo`, `mar`.`id` as `id_marca`
        FROM `manual` AS `man` INNER JOIN `modelos` AS `mod` ON `man`.`id_modelo` = `mod`.`id` INNER JOIN `marcas` AS `mar` ON `man`.`id_marca` = `mar`.`id`
        WHERE `man`.`active` = 1");
    }
}
