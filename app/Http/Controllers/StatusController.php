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
            DB::insert('INSERT INTO `resposta` (`resposta`, `id_pergunta`, `id_user`, `data`) VALUES (?, ?, ?, ?)', 
            [$request->resposta, $request->id_pergunta, $request->id_user, NOW()]);
            $list = $this->getPerguntas($request, $request->id_programacao);
            $message = 'Resposta inserida com sucesso!';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function savePergunta(Request $request) {
        try {
            DB::insert('INSERT INTO `pergunta` (`pergunta`, `id_programacao`, `id_user`, `data`) VALUES (?, ?, ?, ?)', 
            [$request->pergunta, $request->id_programacao, $request->id_user, NOW()]);

            $list = $this->getPerguntas($request, $request->id_programacao);
            $message = 'Pergunta enviada com sucesso!';
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
            DB::update('UPDATE `status` SET `active` = ? WHERE id = ?', [0, $id]);
            $list = DB::select("SELECT * FROM `status` WHERE `active` = 1");
            $message = 'Status deletado com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function edit(Request $request) {
        try {
            DB::update('UPDATE `status` SET `nome` = ?, `porcentagem` = ? WHERE id = ?', [$request->nome, $request->porcentagem, $request->id]);
            $list = DB::select("SELECT * FROM `status` WHERE `active` = 1");
            $message = 'Status alterado com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function getPerguntas(Request $request, $id) {
        try {
            $arrList = array();
            $arrPerguntas = DB::select("SELECT `pergunta`.*, `users`.`name`, `users`.`email`, 
                DATE_FORMAT(`pergunta`.`data`, '%d/%m/%Y %H:%i') AS `data_pergunta` 
                FROM `pergunta` 
                INNER JOIN `users` ON `pergunta`.`id_user` = `users`.`id` 
                INNER JOIN `programacao` ON `pergunta`.`id_programacao` = `programacao`.`id` 
                WHERE `id_programacao` = ?
                ORDER BY `pergunta`.`id` DESC", [$id]);

            foreach($arrPerguntas as $key => $pergunta) {

                $arrList[$key]['pergunta'] = $pergunta;

                $arrRespostas = DB::select("SELECT `resposta`.*, `users`.`name`, `users`.`email`, 
                    DATE_FORMAT(`resposta`.`data`, '%d/%m/%Y %H:%i') AS `data_resposta` 
                    FROM `resposta` 
                    INNER JOIN `users` ON `resposta`.`id_user` = `users`.`id` 
                    INNER JOIN `pergunta` ON `pergunta`.`id` = `resposta`.`id_pergunta` 
                    WHERE `id_pergunta` = ?
                    ORDER BY `resposta`.`id` ASC", [$pergunta->id]);

                $arrList[$key]['respostas'] = $arrRespostas;
            }

            return $arrList;
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }
}
