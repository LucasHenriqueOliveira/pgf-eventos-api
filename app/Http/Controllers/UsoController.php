<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UsoController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT * FROM `condicoes_uso` WHERE `active` = 1");
    }

    public function save(Request $request) {
        try {
            DB::insert('INSERT INTO `condicoes_uso` (`pergunta`) VALUES (?)', [$request->pergunta]);
            $list = DB::select("SELECT * FROM `condicoes_uso` WHERE `active` = 1");
            $message = 'Condições de uso inserido com sucesso.';
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
            DB::update('UPDATE condicoes_uso SET `active` = ? WHERE id = ?', [0, $id]);
            $list = DB::select("SELECT * FROM `condicoes_uso` WHERE `active` = 1");
            $message = 'Condições de uso deletado com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }
}
