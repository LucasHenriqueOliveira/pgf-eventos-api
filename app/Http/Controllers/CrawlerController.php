<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CrawlerController extends Controller
{
    public function process(Request $request) {
        switch ($request->type) {
            case 'marcas':
                try {
                    for ($i = 0; $i < count($request->data); $i++) {
                        DB::insert('INSERT INTO `marcas` (`id`, `nome`, `key`) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE `nome` = ?', [$request->data[$i]['id'], 
                        $request->data[$i]['fipe_name'], $request->data[$i]['key'],
                        $request->data[$i]['fipe_name']]);
                    }
                    return $this->successResponse();
                } catch (Exception $e) {
                    return $this->failedResponse();
                }
                break;
            case 'modelos':
                try {
                    $marcas = DB::select("SELECT * FROM `marcas` where id > 127");
                    
                    foreach ($marcas as $marca) {
                        $url = 'http://fipeapi.appspot.com/api/1/carros/veiculos/' . $marca->id . '.json';

                        $client = new Client(); //GuzzleHttp\Client
                        $result = $client->get($url);
                        $arr = json_decode($result->getBody(), true);
                        
                        for ($i = 0; $i < count($arr); $i++) {
                            DB::insert('INSERT INTO `modelos` (`id`, `nome`, `key`, `id_marca`) VALUES (?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE `nome` = ?', [$arr[$i]['id'], 
                            $arr[$i]['fipe_name'], $arr[$i]['key'], $marca->id,
                            $arr[$i]['fipe_name']]);
                        }
                    }

                    return $this->successResponse();
                } catch (Exception $e) {
                    return $this->failedResponse();
                }
                break;
        }
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Ocorreu um erro na operação.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse() {
        return response()->json([
            'data' => 'Operação realizada com sucesso.'
        ], Response::HTTP_OK);
    }

}