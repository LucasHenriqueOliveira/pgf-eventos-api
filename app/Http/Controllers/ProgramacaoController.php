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

    public function getEvento(Request $request, $id) {
        $arrList = array();
        $arrProgramacao = DB::select("SELECT *
            FROM `programacao` INNER JOIN `palestrante` 
            ON `programacao`.`id_palestrante` =  `palestrante`.`id` 
            WHERE `programacao`.`id` = ?", [$id]);

        $inscritos = DB::select("SELECT count(*) AS `inscritos` FROM `users_programacao` 
            WHERE `id_programacao` = ? AND `ativo`= 1", [$id])[0];

        $arrData['evento'] = $arrProgramacao[0];
        $arrData['evento']->inscritos = $inscritos->inscritos;
        
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
        $arrData['perguntas'] = $arrList;

        return $arrData;
    }

    public function setInscricao(Request $request) {
        try {
            /*
            $evento = DB::select("SELECT * FROM `programacao` WHERE `id` = ?", [$request->id_programacao])[0];
            $eventosInscritos = DB::select("SELECT * FROM `users_programacao` INNER JOIN `programacao` 
                ON `programacao`.`id` = `users_programacao`.`id_programacao` 
                WHERE `users_programacao`.`id_user` = ? AND `users_programacao`.`ativo` = 1", 
                [$request->id_user]);

            $hora_inicio_evento = strtotime($evento->hora_inicio);
            $hora_fim_evento = strtotime($evento->hora_fim);
            
            foreach($eventosInscritos as $key => $programacao) {
                $hora_inicio = strtotime($programacao->hora_inicio);
                $hora_fim = strtotime($programacao->hora_fim);
                
                if ($programacao->dia === $evento->dia && (($hora_inicio_evento < $hora_fim) || ($hora_fim_evento > $hora_inicio))) { 
                    return response()->json([
                        'error' => 'Usuário já inscrito em outra oficina neste horário.'
                    ], Response::HTTP_NOT_FOUND);
                }
            }
            */


            DB::insert('INSERT INTO `users_programacao` (`id_user`, `id_programacao`) VALUES (?, ?)', 
                [$request->id_user, $request->id_programacao]);

            $list = app(\App\Http\Controllers\UsuarioController::class)->getUser($request, $request->id_user);
            $message = 'Inscrição realizada com sucesso!';

            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function removeInscricao(Request $request) {
        try {
            DB::update('UPDATE `users_programacao` SET `ativo` = 0 WHERE `id_user` = ? AND `id_programacao` = ?',
                [$request->id_user, $request->id_programacao]);

            $list = app(\App\Http\Controllers\UsuarioController::class)->getUser($request, $request->id_user);
            $message = 'Inscrição cancelada com sucesso!';

            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function finalizar(Request $request) {
        try {
            DB::update('UPDATE `programacao` SET `conclusao` = ?, `votacao` = 1 WHERE `id` = ?',
                [$request->conclusao, $request->id]);

            $list = $this->get($request);
            $message = 'Oficina finalizada com sucesso!';

            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function desabilitar(Request $request) {
        try {
            DB::update('UPDATE `programacao` SET `votacao` = 0 WHERE `id` = ?',
                [$request->id]);

            $list = $this->get($request);
            $message = 'Desabilitada a votação da oficina com sucesso!';

            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function getVotacao(Request $request, $id) {
        $arrList = array();
        $arr = array();

        $arrOficinas = DB::select("SELECT *, `programacao`.`id` AS `id_programacao` 
        FROM `programacao` INNER JOIN `palestrante` 
        ON `programacao`.`id_palestrante` =  `palestrante`.`id` WHERE `programacao`.`ativo` = 1
        AND `programacao`.`votacao` = 1 AND `programacao`.`tipo` = 'Oficina'
        ORDER BY `programacao`.`id` ASC");

        $arrList['oficinas'] = $arrOficinas;

        $arrVotados = DB::select("SELECT * FROM `votacao` WHERE `id_user` = ?", [$id]);
        $arrList['votados'] = $arrVotados;

        return $arrList;
    }

    public function getVotacaoDetalhada(Request $request, $id, $id_user) {

        $result = DB::select("SELECT *, `programacao`.`id` AS `id_programacao` 
        FROM `programacao` INNER JOIN `palestrante` ON `programacao`.`id_palestrante` =  `palestrante`.`id`
        WHERE `programacao`.`id` = ?", [$id]);

        $arrVoto = DB::select("SELECT * FROM `votacao` WHERE `id_user` = ? AND `id_programacao` = ?", [$id_user, $id]);
        
        if(count($arrVoto)) {
            $result[0]->voto = $arrVoto[0]->voto;
        } else {
            $result[0]->voto = null;
        }

        return $result;
    }

    public function saveVoto(Request $request) {
        try {

            $result = DB::select("SELECT * FROM `votacao` WHERE `id_user` = ? AND `id_programacao` = ?", [$request->id_user, $request->id_programacao]);

            if(count($result)) {
                DB::update('UPDATE `votacao` SET `voto` = ?, `data` = ? WHERE `id` = ?', [$request->voto, NOW(), $result[0]->id]);
            } else {
                DB::insert('INSERT INTO `votacao` (`id_user`, `id_programacao`, `voto`, `data`) VALUES (?, ?, ?, ?)', 
                [$request->id_user, $request->id_programacao, $request->voto, NOW()]);
            }

            $list = [];
            $message = 'Voto salvo com sucesso!';

            return $this->successResponse($list, $message);
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

}