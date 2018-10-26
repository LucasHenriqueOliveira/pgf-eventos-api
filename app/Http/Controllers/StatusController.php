<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT * FROM `status` WHERE `active` = 1");
    }

    public function save(Request $request) {
        try {
            DB::insert('INSERT INTO `status` (`nome`, `porcentagem`) VALUES (?, ?)', [$request->nome, $request->porcentagem]);
            $list = DB::select("SELECT * FROM `status` WHERE `active` = 1");
            return $this->successResponse($list);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Ocorreu um erro na operação.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse($data) {
        return response()->json([
            'data' => $data,
            'message' => 'Status inserido com sucesso.'
        ], Response::HTTP_OK);
    }
}
