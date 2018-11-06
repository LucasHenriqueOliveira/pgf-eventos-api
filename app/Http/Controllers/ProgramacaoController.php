<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

class ProgramacaoController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT *, `programacao`.`id` AS `id_programacao` 
        FROM `programacao` INNER JOIN `palestrante` 
        ON `programacao`.`id_palestrante` =  `palestrante`.`id` WHERE `programacao`.`ativo` = 1
        ORDER BY `programacao`.`id` ASC");
    }

    public function getListProgramacao(Request $request) {
        $arrList = array();
        $arrData = array();
        $arrProgramacao = DB::select("SELECT *, CONCAT(SUBSTRING(`dia`, 7, 4), SUBSTRING(`dia`, 4, 2), SUBSTRING(`dia`, 1, 2)) 
            AS `dia_order` FROM `programacao` WHERE `ativo` = 1 ORDER BY `dia_order`, `hora_inicio` ASC");

        foreach($arrProgramacao as $key => $programacao) {
            if (!in_array($programacao->dia, $arrData)) { 
                array_push($arrData, $programacao->dia);
            }
        }

        foreach($arrData as $key => $data) {
            $arrList[$key]['data'] = $data;
            $arrList[$key]['programacao'] = [];
        }

        foreach($arrList as $key => $data) {
            
            foreach($arrProgramacao as $programacao) {

                if($data['data'] == $programacao->dia) {
                    array_push($arrList[$key]['programacao'], $programacao);
                }
            }
        }
        

        return $arrList;
    }

    public function getList(Request $request, $id) {
        return DB::select("SELECT `name`, `email` 
        FROM `programacao` INNER JOIN `users_programacao` 
        ON `programacao`.`id` =  `users_programacao`.`id_programacao` INNER JOIN `users`
        ON `users`.`id` =  `users_programacao`.`id_user` WHERE `programacao`.`id` = ? AND
        `users_programacao`.`ativo` = ?", [$id, 1]);
    }

    public function save(Request $request) {
        if ($request->hasFile('documento')) {
            $file = $request->file('documento');
            $md5Name = md5_file($request->file('documento')->getRealPath());
            $guessExtension = $request->file('documento')->guessExtension();
            $destination_path = public_path('files'); 
            $name = $md5Name.'_' . time() . '.' . $guessExtension;

            try {
                DB::insert('INSERT INTO `programacao` (`titulo`, `dia`, `hora_inicio`, `hora_fim`, `local`,
                `id_palestrante`, `tipo`, `vagas`, `documento`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [$request->titulo, $request->dia, $request->hora_inicio, $request->hora_fim,
                $request->local, $request->selectedPalestrante, $request->selectedTipo, $request->vagas, $name]);
                $list = DB::select("SELECT * FROM `programacao` WHERE `ativo` = 1");
                $message = 'Programação inserida com sucesso.';
                $file->move($destination_path, $name);

                return $this->successResponse($list, $message);
            } catch (Exception $e) {
                return $this->failedResponse();
            }
        } else {
            try {
                DB::insert('INSERT INTO `programacao` (`titulo`, `dia`, `hora_inicio`, `hora_fim`, `local`,
                `id_palestrante`, `tipo`, `vagas`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$request->titulo, $request->dia, $request->hora_inicio, $request->hora_fim,
                $request->local, $request->selectedPalestrante, $request->selectedTipo, $request->vagas]);
                $list = DB::select("SELECT * FROM `programacao` WHERE `ativo` = 1");
                $message = 'Programação inserida com sucesso.';

                return $this->successResponse($list, $message);
            } catch (Exception $e) {
                return $this->failedResponse();
            }
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
            DB::update('UPDATE programacao SET `ativo` = ? WHERE id = ?', [0, $id]);
            $list = DB::select("SELECT * FROM `programacao` WHERE `ativo` = 1");
            $message = 'Programação removida com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function edit(Request $request) {
        try {
            if ($request->hasFile('documento')) {
                $file = $request->file('documento');
                $md5Name = md5_file($request->file('documento')->getRealPath());
                $guessExtension = $request->file('documento')->guessExtension();
                $destination_path = public_path('files'); 
                $name = $md5Name.'_' . time() . '.' . $guessExtension;

                DB::update('UPDATE programacao SET `titulo` = ?, `dia` = ?, `hora_inicio` = ?, `hora_fim` = ?, `local` = ?,
                `id_palestrante` = ?, `tipo` = ?, `vagas` = ?, `documento` = ? WHERE id = ?', [$request->titulo, $request->dia, 
                $request->hora_inicio,  $request->hora_fim,  $request->local,  $request->palestrante,  
                $request->tipo, $request->vagas, $name, $request->id]);

                $file->move($destination_path, $name);

            } else {
                DB::update('UPDATE programacao SET `titulo` = ?, `dia` = ?, `hora_inicio` = ?, `hora_fim` = ?, `local` = ?,
                `id_palestrante` = ?, `tipo` = ?, `vagas` = ? WHERE id = ?', [$request->titulo, $request->dia, 
                $request->hora_inicio,  $request->hora_fim,  $request->local,  $request->palestrante,  
                $request->tipo, $request->vagas, $request->id]);
            }
            
            $list = $this->get($request);
            $message = 'Programação alterada com sucesso.';
            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

}