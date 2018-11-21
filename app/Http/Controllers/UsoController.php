<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UsoController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT * FROM `palestrante` WHERE `ativo` = 1 ORDER BY `nome` ASC");
    }

    public function getPalestrante(Request $request, $id) {
        try {
            $arrList = array();
            $palestrante = DB::select("SELECT * FROM `palestrante` WHERE `ativo` = 1 AND id = ?", [$id])[0];

            $arrList['palestrante'] = $palestrante;

            $arrProgramacao = DB::select("SELECT *, CONCAT(SUBSTRING(`dia`, 7, 4), SUBSTRING(`dia`, 4, 2), SUBSTRING(`dia`, 1, 2)) 
                AS `dia_order`
                FROM `programacao`
                WHERE `id_palestrante` = ?
                ORDER BY `dia_order`, `hora_inicio` ASC", [$palestrante->id]);

            $arrList['programacao'] = $arrProgramacao;

            return $arrList;
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function save(Request $request) {
        try {
            DB::insert('INSERT INTO `palestrante` (`nome`, `sobre`) VALUES (?, ?)', [$request->nome, $request->sobre]);
            $list = DB::select("SELECT * FROM `palestrante` WHERE `ativo` = 1");
            $message = 'Palestrante inserido com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Ocorreu um erro na operação.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse($data, $message) {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], Response::HTTP_OK);
    }

    public function remove(Request $request, $id) {
        try {
            DB::update('UPDATE palestrante SET `ativo` = ? WHERE id = ?', [0, $id]);
            $list = DB::select("SELECT * FROM `palestrante` WHERE `ativo` = 1");
            $message = 'Palestrante removido com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function edit(Request $request) {
        try {
            DB::update('UPDATE palestrante SET `nome` = ?, `sobre` = ? WHERE id = ?', [$request->nome, $request->sobre, $request->id]);
            $list = DB::select("SELECT * FROM `palestrante` WHERE `ativo` = 1");
            $message = 'Palestrante alterado com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }
}
