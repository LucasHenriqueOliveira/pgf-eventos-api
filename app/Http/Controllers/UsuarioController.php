<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Mailgun\Mailgun;
use App\Mail\ChangePassword;

class UsuarioController extends Controller
{
    public function get(Request $request) {
        return DB::select("SELECT `name`, `email` FROM `users`");
    }

    public function getUser(Request $request, $id) {
        return DB::select("SELECT * FROM `users_programacao` 
        	INNER JOIN `programacao` ON `users_programacao`.`id_programacao` = `programacao`.`id`
        	WHERE `users_programacao`.`id_user` = ? AND `users_programacao`.`ativo` = 1", [$id]);
    }

    public function signup(Request $request) {
    	try {
    		$user = DB::select("SELECT * FROM `users` WHERE `email` = ?", [$request->email]);

    		if (!count($user)) {
                $token = md5(uniqid($request->email, true));

    			DB::insert('INSERT INTO `users` (`name`, `email`, `password`, `created_at`, `token`) VALUES (?, ?, ?, ?, ?)', 
	            [$request->name, $request->email, Hash::make($request->password), NOW(), $token]);

	            $this->sendEmail($request->name, $request->email, $token);
	            
	            return $this->successResponse();

    		} else {
				return response()->json([
            		'error' => 'Usuário já cadastrado.'
				], Response::HTTP_NOT_FOUND);
    		}
            
        } catch (Exception $e) {
            return $this->failedResponse();
        }
    }

    public function sendEmail($name, $email, $token){

        $url = 'https://pfe-eventos.herokuapp.com/api/users/validar/'.$token;

        $texto = '<br /> Prezado(a) ' . $name . ',';
        $texto .= '<br /><br />Falta pouco para finalizar a verificação da sua conta. Clique no link abaixo e faça a validação:';
        $texto .= '<br /><br /> <a href="'. $url .'">Validar o meu cadastro</a>';
        $texto .= '<br /><br /> Att, <br />PFE INSS';
        $texto .= '<br /><br /> <h5>Não responda a este email. Os emails enviados a este endereço não serão respondidos.</h5>';


    	$mg = Mailgun::create(getenv("MAILGUN_KEY"));
        $mg->messages()->send(getenv("MAILGUN_DOMAIN"), [
            'from' => "PFE INSS <postmaster@cidadaniaativa.com.br>",
            'to'      => $email,
            'subject' => 'Cadastro de Usuário',
            'html'    => $texto
        ]);
    }

    public function validarUser(Request $request, $token){
        $user = DB::select("SELECT * FROM `users` WHERE `token` = ?", [$token]);

        if(count($user)) {
            DB::update('UPDATE `users` SET `ativo` = ? WHERE id = ?', [1, $user[0]->id]);

            return new ConfirmUserMail('Usuário validado com sucesso. Favor abrir o aplicativo e fazer login.');

        } else {
            return new ConfirmUserMail('A validação falhou. Tente novamente!');
        }
    }

    public function passwordReset(Request $request, $token){
        $user = DB::select("SELECT * FROM `password_resets` WHERE `token` = ?", [$token]);

        if(count($user)) {
            
            return new ChangePassword($user[0]->email, '');

        } else {
            return new ChangePassword('', 'Usuário não encontrado!');
        }
    }

    public function failedResponse(){
        return response()->json([
            'error' => 'Ocorreu um erro na operação.'
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse() {
        return response()->json([
            'message' => 'Confira o seu email e valide o cadastro.'
        ], Response::HTTP_OK);
    }
}
